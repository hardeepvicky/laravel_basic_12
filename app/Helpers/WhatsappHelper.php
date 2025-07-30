<?php

namespace App\Helpers;

use App\Exceptions\ApiException;
use App\Exceptions\UserException;
use App\Models\EmailLog;
use App\Models\WhatsappLog;
use Exception;
use Illuminate\Support\Facades\App;
use Mailjet\Resources;

class WhatsappHelper
{
    const EMAIL_VIA_INTERAKT = 'interakt';

    private static $enabled_api_platforms = [self::EMAIL_VIA_INTERAKT];

    private String $via, $country_code, $template_name, $template_lanugage;

    private array $header_values = [], $body_values = [], $button_values = [], $mobile_list = [];

    private bool $is_sandbox = false;

    private $api_key, $api_secret;

    private WhatsappLog $logModel;

    public function __construct()
    {
        $via = env("WHATSAPP_API_PLATFORM");

        if (!$via) {
            throw new UserException("env : EMAIL_API_PLATFORM not set");
        }

        $this->via = $via;

        if (!in_array($this->via, self::$enabled_api_platforms)) {
            throw new UserException("$this->via is not enabled");
        }

        if ($this->via == self::EMAIL_VIA_INTERAKT) {
            $api_secret = env('INTERAKT_API_TOKEN');

            if (!$api_secret) {
                throw new UserException("env : INTERAKT_API_TOKEN is not set");
            }

            $this->api_secret = $api_secret;
        }

        $this->is_sandbox = App::isLocal();
    }

    public function setTemplate(String $template_name, String $template_lanugage)
    {
        $this->template_name = $template_name;
        $this->template_lanugage = $template_lanugage;

        return $this;
    }

    public function setHeaderValues($arg)
    {
        $this->header_values = $arg;

        return $this;
    }

    public function setBodyValues($arg)
    {
        $this->body_values = $arg;

        return $this;
    }

    public function setButtonValues($arg)
    {
        $this->button_values = $arg;

        return $this;
    }

    public function setCountryCode($arg)
    {
        $this->country_code = $arg;

        return $this;
    }

    public function addTo(String $mobile_no, String $name, $user_id)
    {
        foreach ($this->mobile_list as $to) {
            if (isset($to['mobile_no']) && $to['mobile_no'] == $mobile_no) {
                return $this;
            }
        }

        $this->mobile_list[] = [
            "mobile_no" => $mobile_no,
            "name" => $name,
            "user_id" => $user_id
        ];

        return $this;
    }

    public function setSandbox(bool $value)
    {
        $this->is_sandbox = $value;

        return $this;
    }

    public function saveToLog($log_extras = [])
    {
        $this->logModel = new WhatsappLog();

        $log = array_merge($log_extras, [
            "api_platform" => $this->via,
            "template_name" => $this->template_name,
            "template_lanugage" => $this->template_lanugage,
            "country_code" => $this->country_code,
            "header_values" => $this->header_values,
            "body_values" => $this->body_values,
            "button_values" => $this->button_values,
            "to" => $this->mobile_list,
            "is_sandbox" => (int) $this->is_sandbox,
            "is_sent" => null
        ]);

        $this->logModel->fill($log);

        if (!$this->logModel->save()) {
            throw new Exception("Email Log Save Failed");
        }

        return $this->logModel->id;
    }

    public function updateLog($log)
    {
        if (!$this->logModel) {
            throw new UserException("developer forgot to call saveToLog()");
        }

        $this->logModel->fill($log);

        if (!$this->logModel->save()) {
            throw new UserException("Email Log Save Failed", UserException::ERROR_LARAVEL_FAIL);
        }
    }

    public function setApiKey(String $api_key)
    {
        $this->api_key = $api_key;
    }

    public function setApiSecret(String $api_secret)
    {
        $this->api_secret = $api_secret;
    }

    public function send(array $log_extras = [])
    {
        $result = [
            "total" => 0,
            "success" => 0,
            "fail" => 0,
            "errors" => []
        ];

        try {
            $this->saveToLog($log_extras);

            if (!$this->is_sandbox)
            {
                if (!check_internet_connection())
                {
                    throw new UserException("Internet not working", UserException::ERROR_NETWORK_FAIL);
                }

                switch ($this->via) {
                    case self::EMAIL_VIA_INTERAKT:
                        $api_result = $this->_sendViaInterAkt();
                        $result = array_merge($result, $api_result);
                        break;

                    default:
                        throw new UserException("No Via found");
                }
            }
        } 
        catch (UserException $ex) {
            $this->updateLog([
                "is_sent" => 0,
                "error_msg" => $ex->getMessage(),
                "error_code" => $ex->getCode(),
                "error_data" => $ex->data
            ]);

            $result['errors'][] = $ex->getMessage();
        }
        catch (Exception $ex) {
            $this->updateLog([
                "is_sent" => 0,
                "error_msg" => $ex->getMessage(),
                "error_code" => $ex->getCode()
            ]);

            $result['errors'][] = $ex->getMessage();

            throw $ex;
        }

        return $result;
    }

    private function _sendViaInterAkt()
    {
        $result = [
            "total" => 0,
            "success" => 0,
            "fail" => 0,
            "errors" => []
        ];

        $to_list = [];

        foreach ($this->mobile_list as $to) {
            array_check_key_and_throw_error($to, ["mobile_no"]);

            $to_list[] = $to['mobile_no'];
        }

        $result['total'] = count($to_list);

        if (!$result['total']) {
            return $result;
        }

        $request_params = [
            "countryCode" => $this->country_code,
            "phoneNumber" => $to_list,
            "type" => "Template",
            "template" => [
                "name" => $this->template_name,
                "languageCode" => $this->template_lanugage,
                "bodyValues" => $this->body_values,
                "buttonValues" => $this->button_values
            ]
        ];

        if ($this->header_values)
        {
            $request_params['template']['headerValues'] = $this->header_values;
        }

        // d($request_params, true);

        $request_params = json_encode($request_params);

        $headers = [
            'Authorization: Basic ' . $this->api_secret,
            'Content-Type: application/json'
        ];

        $url = "https://api.interakt.ai/v1/public/message/";

        // d($request_params, true);

        // d($headers, true);

        $raw_response = curl_post_request($url, $request_params, $headers);

        $response = json_decode($raw_response);

        if ($response === null && json_last_error() !== JSON_ERROR_NONE)
        {
            throw new UserException("can not json decode the Response of $url", UserException::ERROR_API_FAIL, [
                "response" => $raw_response
            ]);
        }

        $api_response = Util::objToArray($response);

        $this->updateLog([
            "is_sent" => (int) $api_response['result'],
            "api_response" => $api_response
        ]);

        if ($api_response['result'])
        {
            $result['success'] = count($to_list);
        }
        else
        {
            $result['fail'] = count($to_list);
        }

        return $result;
    }
}