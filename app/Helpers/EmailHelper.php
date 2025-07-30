<?php

namespace App\Helpers;

use App\Exceptions\ApiException;
use App\Exceptions\UserException;
use App\Models\EmailLog;
use Exception;
use Illuminate\Support\Facades\App;
use Mailjet\Resources;

class EmailHelper
{
    const EMAIL_VIA_BREVO = 'brevo';
    const EMAIL_VIA_MAILJET = 'mailjet';

    private static $enabled_api_platforms = [self::EMAIL_VIA_MAILJET];

    private String $email_via, $from_email, $from_name, $subject, $html, $type;
    private array $cc_list = [], $to_list = [], $attachments = [];
    private bool $is_sandbox = false;

    private $api_key, $api_secret;

    private $emailLog;

    public function __construct()
    {
        $this->email_via = env("EMAIL_API_PLATFORM");

        if (!$this->email_via) {
            throw_exception("env : EMAIL_API_PLATFORM not set");
        }

        if (!in_array($this->email_via, self::$enabled_api_platforms)) {
            throw_exception("email_via : $this->email_via is not enabled");
        }

        if ($this->email_via == self::EMAIL_VIA_MAILJET) {
            $api_key = env('MAILJET_API_KEY');

            if (!$api_key) {
                throw_exception("env : MAILJET_API_KEY is not set");
            }

            $this->api_key = $api_key;

            $api_secret = env('MAILJET_API_SECRET');

            if (!$api_secret) {
                throw_exception("env : MAILJET_API_SECRET is not set");
            }

            $this->api_secret = $api_secret;
        } else if ($this->email_via == self::EMAIL_VIA_BREVO) {
            $api_key = env('BREVO_API_KEY');

            if (!$api_key) {
                throw_exception("env : BREVO_API_KEY is not set");
            }

            $this->api_key = $api_key;
        }

        $this->from_email = env("EMAIL_FROM");
        $this->from_name = env("EMAIL_NAME");

        $this->is_sandbox = App::isLocal();
    }

    public function setSubject(String $subject)
    {
        if (strlen($subject) > 80) {
            throw new Exception("Subject length should be less than 80 chars");
        }

        $this->subject = $subject;

        return $this;
    }

    public function setBody(String $html)
    {
        $this->html = $html;

        return $this;
    }

    public function setType(String $type)
    {
        if (strlen($type) > 80) {
            throw new Exception("type length should be less than 80 chars");
        }

        $this->type = $type;

        return $this;
    }

    public function setFrom(String $from_email, String $from_name)
    {
        $this->from_email = $from_email;
        $this->from_name = $from_name;

        return $this;
    }

    public function addTo(String $email, String $name)
    {
        foreach ($this->to_list as $to) {
            if (isset($to['email']) && $to['email'] == $email) {
                return $this;
            }
        }

        $this->to_list[] = [
            "email" => $email,
            "name" => $name
        ];

        return $this;
    }

    public function addCc(String $email, String $name)
    {
        foreach ($this->cc_list as $cc) {
            if (isset($cc['email']) && $cc['email'] == $email) {
                return $this;
            }
        }

        $this->cc_list[] = [
            "email" => $email,
            "name" => $name
        ];

        return $this;
    }

    public function addAttachment($file, $filename = "")
    {
        if (!file_exists($file)) {
            throw new Exception("file $file is not exist");
        }

        if (!$filename) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
        }

        $ext = pathinfo($file, PATHINFO_EXTENSION);

        $filename = $filename . "." . $ext;

        foreach ($this->attachments as $attachment) {
            if ($attachment['filename'] == $filename) {
                return $this;
            }
        }

        $this->attachments[] = [
            "filename" => $filename,
            "mime_type" => get_mime_type_from_extension($ext),
            "base64" => base64_encode(file_get_contents($file))
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
        $this->validate();

        $this->emailLog = new EmailLog();

        $path =  $this->emailLog->getStoragePath();

        $file = $this->emailLog->getNextId() . ".html";

        $disk = EmailLog::getStorageDisk();

        $disk->put($path . $file, $this->html);

        $to_names = $to_emails = [];
        foreach ($this->to_list as $to) {
            $to_names[] = $to['name'];
            $to_emails[] = $to['email'];
        }

        $cc_names = $cc_emails = [];
        foreach ($this->cc_list as $to) {
            $cc_names[] = $to['name'];
            $cc_emails[] = $to['email'];
        }

        $log = array_merge($log_extras, [
            "type" => $this->type,
            "from_name" => $this->from_name,
            "from_email" => $this->from_email,
            "to_name" => implode(",", $to_names),
            "to_email" => implode(",", $to_emails),
            "cc_name" => implode(",", $cc_names),
            "cc_email" => implode(",", $cc_emails),
            "subject" => $this->subject,
            "content_file" => $path . $file
        ]);

        $this->emailLog->fill($log);

        if (!$this->emailLog->save()) {
            throw new Exception("Email Log Save Failed");
        }

        return $this->emailLog->id;
    }

    public function updateLog($log)
    {
        if (!$this->emailLog) {
            throw_exception("you forgot to call saveToLog()");
        }

        $this->emailLog->fill($log);

        if (!$this->emailLog->save()) {
            throw_exception("Email Log Save Failed");
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

    public function validate()
    {
        if (!$this->from_email) {
            throw_exception("from_email is not set");
        }

        if (!$this->from_name) {
            throw_exception("from_name is not set");
        }

        if (!$this->to_list) {
            throw_exception("to email list is not set");
        }

        if (!$this->subject) {
            throw_exception("subject is not set");
        }

        if (!$this->html) {
            throw_exception("html is not set");
        }
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

            if (!check_internet_connection())
            {
                throw new UserException("Internet not working", UserException::ERROR_NETWORK_FAIL);
            }

            switch ($this->email_via) {
                case self::EMAIL_VIA_BREVO:
                    $api_result = $this->_sendViaBrevo();
                    $result = array_merge($result, $api_result);
                    break;

                case self::EMAIL_VIA_MAILJET:
                    $api_result = $this->_sendViaMailjet();
                    $result = array_merge($result, $api_result);
                    break;

                default:
                    throw new Exception("No Email Via found");
            }
        } catch (ApiException $ex) {
            $this->updateLog([
                "is_sent" => 0,
                "error_msg" => $ex->api_error_msg,
                "error_code" => $ex->getCode(),
                "api_response_code" => $ex->api_error_code
            ]);

            $result['errors'][] = $ex->getMessage();
        } catch (Exception $ex) {
            $this->updateLog([
                "is_sent" => 0,
                "error_msg" => $ex->getMessage(),
                "error_code" => $ex->getCode(),
            ]);

            throw $ex;
        }

        return $result;
    }

    private function _sendViaBrevo()
    {
        $data = array(
            'sender' => [
                'email' => $this->from_email,
                'name' => $this->from_name
            ],
            'to' => $this->to_list,
            'subject' => $this->subject,
            'htmlContent' => $this->html
        );

        $config = \Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $this->api_key);

        $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(new \GuzzleHttp\Client(), $config);

        $sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail($data);

        try {
            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);

            $msg_id = $result->getMessageId();
            if ($msg_id) {
                return $msg_id;
            }
        } catch (Exception $ex) {
            throw $ex;
        }

        return false;
    }

    private function _sendViaMailjet()
    {
        $result = [
            "total" => 0,
            "success" => 0,
            "fail" => 0,
            "errors" => []
        ];

        $to_list = [];

        foreach ($this->to_list as $to) {
            array_check_key_and_throw_error($to, ["name", "email"]);

            $to_list[] = [
                "Email" => $to['email'],
                "Name" => $to['name'],
            ];
        }

        $result['total'] = count($to_list);

        if (!$result['total']) {
            return $result;
        }

        $cc_list = [];

        foreach ($this->cc_list as $to) {
            array_check_key_and_throw_error($to, ["name", "email"]);

            $cc_list[] = [
                "Email" => $to['email'],
                "Name" => $to['name'],
            ];
        }

        $attachments = [];

        foreach ($this->attachments as $attachment) {
            array_check_key_and_throw_error($attachment, ["filename", "mime_type", "base64"]);

            $attachments[] = [
                'Filename' => $attachment['filename'],
                'ContentType' => $attachment['mime_type'],
                'Base64Content' => $attachment['base64'],
            ];
        }

        $mj = new \Mailjet\Client($this->api_key, $this->api_secret, true, ['version' => 'v3.1']);

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $this->from_email,
                        'Name' => $this->from_name
                    ],
                    'To' => $to_list,
                    "Cc" => $cc_list,
                    'Subject' => $this->subject,
                    'HTMLPart' => $this->html,
                    "Attachments" => $attachments
                ]
            ],
            "SandboxMode" => $this->is_sandbox
        ];

        // All resources are located in the Resources class
        $response = $mj->post(Resources::$Email, ['body' => $body]);

        //d(get_class_info(get_class($response)), true);

        $api_response = $response->getData();

        $this->updateLog([
            "is_sandbox" => (int) $body['SandboxMode'],
            "is_sent" => (int) $response->success(),
            "api_response" => $api_response
        ]);

        if ($response->success()) {
            if (!$api_response) {
                throw new ApiException('API : No Response on success', $response->getStatus());
            } else if (is_array($api_response) && !isset($api_response['Messages'])) {
                throw new ApiException('API : Response->Messages on success', $response->getStatus());
            } else {
                foreach ($api_response['Messages'] as $message) {
                    if ($message['Status'] == "success") {

                        foreach ($message['To'] as $to) {
                            if ($to['MessageID']) {
                                $result['success']++;
                            } else {
                                $result['fail']++;
                            }
                        }
                    } else if ($message['Status'] == "error") {
                        foreach ($to_list as $t) {
                            $result['fail']++;
                        }
                    }
                }
            }
        } else {
            $data = $response->getData();
            throw new ApiException($data['ErrorMessage'], $data['StatusCode'], $data);
        }

        return $result;
    }
}
