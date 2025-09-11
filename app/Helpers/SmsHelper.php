<?php

namespace App\Helpers;

use App\Exceptions\ApiException;
use App\Exceptions\UserException;
use App\Models\EmailLog;
use App\Models\SmsLog;
use App\Models\WhatsappLog;
use Exception;
use Illuminate\Support\Facades\App;
use Mailjet\Resources;

class SmsHelper
{
    const VIA_DINSTAR = 'dinstar';

    private static $enabled_api_platforms = [self::VIA_DINSTAR];

    private String $via, $country_code, $template_name, $template_lanugage, $mobile_no, $name, $msg;

    private bool $is_sandbox = false;

    private $api_port, $api_user, $api_password, $api_key, $api_secret, $user_id;

    private SmsLog $logModel;

    public function __construct()
    {
        $via = config("app.SMS_API_PLATFORM");

        if (!$via) {
            throw new UserException("app.SMS_API_PLATFORM not set");
        }

        $this->via = $via;

        if (!in_array($this->via, self::$enabled_api_platforms)) {
            throw new UserException("$this->via is not enabled");
        }

        if ($this->via == self::VIA_DINSTAR) {
            $port = config('services.dinstar.port');
            
            if (!$port) {
                throw new UserException("services.dinstar.port is not set");
            }

            $user = config('services.dinstar.user');
            
            if (!$user) {
                throw new UserException("services.dinstar.user is not set");
            }

            $password = config('services.dinstar.password');

            if (!$password) {
                throw new UserException("services.dinstar.user is not set");
            }

            $this->api_port = $port;
            $this->api_user = $user;
            $this->api_password = $password;
        }

        $this->is_sandbox = App::isLocal();
    }

    public function setTemplate(String $template_name, String $template_lanugage)
    {
        $this->template_name = $template_name;
        $this->template_lanugage = $template_lanugage;

        return $this;
    }

    public function setCountryCode($arg)
    {
        $this->country_code = $arg;

        return $this;
    }

    public function setTo(String $mobile_no, String $name, $user_id = null)
    {
        $this->mobile_no = $mobile_no;
        $this->name = $name;
        $this->user_id = $user_id;

        return $this;
    }

    public function setMessage(String $msg)
    {
        $this->msg = $msg;

        return $this;
    }

    public function setSandbox(bool $value)
    {
        $this->is_sandbox = $value;

        return $this;
    }

    public function saveToLog($log_extras = [])
    {
        $this->logModel = new SmsLog();

        $log = array_merge($log_extras, [
            "api_platform" => $this->via,
            "template_name" => $this->template_name,
            "template_lanugage" => $this->template_lanugage,
            "country_code" => $this->country_code,
            "to_mobile" => $this->mobile_no,
            "to_name" => $this->name,
            "to_user_id" => $this->user_id,
            "msg" => $this->msg,
            "is_sandbox" => (int) $this->is_sandbox,
            "is_sent" => null
        ]);

        $this->logModel->fill($log);

        if (!$this->logModel->save()) {
            throw new Exception("SMS Log Save Failed");
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
            throw new UserException("SMS Log Save Failed", UserException::ERROR_LARAVEL_FAIL);
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
                    case self::VIA_DINSTAR:
                        $api_result = $this->_sendDinstar();
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
            ]);

            $result['fail'] = $result['total'];
            $result['errors'][] = $ex->getMessage();
        }
        catch (Exception $ex) {
            $this->updateLog([
                "is_sent" => 0,
                "error_msg" => $ex->getMessage(),
            ]);

            $result['fail'] = $result['total'];
            $result['errors'][] = $ex->getMessage();

            throw $ex;
        }

        return $result;
    }

    private function _sendDinstar()
    {
        $result = [
            "total" => 0,
            "success" => 0,
            "fail" => 0,
            "errors" => []
        ];

        $result['total'] = 1;

        if (!$result['total']) {
            return $result;
        }

        $post = [
            "text" => $this->msg,
            "param" => [
                [
                    "number" => $this->country_code . $this->mobile_no,
                    "user_id" => $this->user_id
                ]
            ],
            "port" => [$this->api_port],
            "encoding" => "unicode"
        ];

        $api_url = config('services.dinstar.api_url');
        $user_pwd = $this->api_user . ":" . $this->api_password;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => 'unicode',
            CURLOPT_USERPWD => $user_pwd,
            CURLOPT_HTTPAUTH => CURLAUTH_ANY,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($post),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $api_response = curl_exec($curl);
        curl_close($curl);
        

        if ($api_response) {
            $response = Util::objToArray(json_decode($api_response));

            if (!isset($response['error_code']))
			{
				throw new UserException('No Response', UserException::ERROR_API_FAIL);
			}

            $this->updateLog([
                "api_response" => $response
            ]);
            
            if ($response['error_code'] >= 200 && $response['error_code'] < 300) 
            {
                $result['success'] = 1;

                $this->updateLog([
                    "is_sent" => 1
                ]);
                
            } 
            else if ($response['error_code'] == 400) 
            {                
                throw new UserException('Request format is not valid', UserException::ERROR_API_FAIL);
            } 
            else if ($response['error_code'] == 413) 
            {
                throw new UserException('the count of telephone numbers is more than 32', UserException::ERROR_API_FAIL);
            } 
            else if ($response['error_code'] >= 500) 
            {
                throw new UserException('unknow error', UserException::ERROR_API_FAIL);
            }
        } 
        else 
        {
            throw new UserException('No Response', UserException::ERROR_API_FAIL);
        }
        
        return $result;
    }
}