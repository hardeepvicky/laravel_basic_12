<?php

namespace App\Helpers;

use App\Models\EmailLog;
use Exception;
use Mailjet\Resources;

class EmailHelper
{
    const EMAIL_VIA_BREVO = 'brevo';
    const EMAIL_VIA_MAILJET = 'mailjet';

    private String $email_via, $from_email, $from_name, $to_email, $to_name, $subject, $html, $type;
    private $from_user_id, $to_user_id;

    public function __construct(String $email_via)
    {
        $this->email_via = $email_via;

        $this->from_email = env("EMAIL_FROM");
        $this->from_name = env("EMAIL_NAME");
    }

    public function setSubject(String $subject)
    {
        if (strlen($subject) > 80) {
            throw new Exception("Subject length should be less than 80 chars");
        }

        $this->subject = $subject;
    }

    public function setBody(String $html)
    {
        $this->html = $html;
    }

    public function setType(String $type)
    {
        if (strlen($type) > 80) {
            throw new Exception("type length should be less than 80 chars");
        }

        $this->type = $type;
    }

    public function setFrom(String $from_email, String $from_name, $from_user_id = null)
    {
        $this->from_email = $from_email;
        $this->from_name = $from_name;
        $this->from_user_id = $from_user_id;
    }

    public function setTo(String $to_email, String $to_name, $to_user_id = null)
    {
        $this->to_email = $to_email;
        $this->to_name = $to_name;
        $this->to_user_id = $to_user_id;
    }

    public function saveToLog()
    {
        if (!$this->from_email) {
            throw_exception("from_email is not set");
        }

        if (!$this->from_name) {
            throw_exception("from_name is not set");
        }

        if (!$this->to_email) {
            throw_exception("to_email is not set");
        }

        if (!$this->to_name) {
            throw_exception("to_name is not set");
        }

        if (!$this->subject) {
            throw_exception("subject is not set");
        }

        if (!$this->html) {
            throw_exception("html is not set");
        }

        $emailLog = new EmailLog();

        $path = EmailLog::getFileSavePath();

        FileUtility::createFolder($path);

        $file = $emailLog->getNextId() . ".html";

        file_put_contents($path . $file, $this->html);

        $emailLog->fill([
            "type" => $this->type,
            "from_name" => $this->from_name,
            "from_email" => $this->from_email,
            "to_name" => $this->to_name,
            "to_email" => $this->to_email,
            "subject" => $this->subject,
            "content_file" => $path . $file
        ]);

        if (!$emailLog->save()) {
            throw new Exception("Email Log Save Failed");
        }

        return $emailLog->id;
    }

    public function send()
    {
        $save_log_id = $this->saveToLog();

        switch ($this->email_via) {
            case self::EMAIL_VIA_BREVO:
                $result = $this->_sendViaBrevo([
                    [
                        'email' => $this->to_email,
                        'name' => $this->to_name
                    ]
                ]);
                break;

            case self::EMAIL_VIA_MAILJET:
                $result = $this->_sendViaMailjet([
                    [
                        'Email' => $this->to_email,
                        'Name' => $this->to_name
                    ]
                ]);
                break;

            default:
                throw new Exception("No Email-Via found");
        }

        if ($result) {
            $emailLog = EmailLog::findOrFail($save_log_id);
            $emailLog->is_sent = true;
            if (!$emailLog->save()) {
                throw new Exception("Email Log Save Failed");
            }
        }
    }

    public function sendToMany(array $to_list)
    {
        foreach ($to_list as $k => $t) {
            if (!isset($t['name'])) {
                throw new Exception("name is not set in to->$k");
            }

            if (!isset($t['email'])) {
                throw new Exception("email is not set in to->$k");
            }

            if (!isset($t['user_id'])) {
                $t['user_id'] = null;
            }

            $this->setTo($t['email'], $t['name'], $t['user_id']);

            $this->saveToLog();
        }

        switch ($this->email_via) {
            case self::EMAIL_VIA_BREVO:
                return $this->_sendViaBrevo($to_list);
                break;

            default:
                throw new Exception("No Email Via found");
        }
    }

    private function _sendViaBrevo($to_list)
    {
        $api_key = env('BREVO_API_KEY');

        if (!$api_key) {
            throw_exception("Brevo APi Key is not set in env");
        }

        //Request payload

        $data = array(
            'sender' => [
                'email' => $this->from_email,
                'name' => $this->from_name
            ],
            'to' => $to_list,
            'subject' => $this->subject,
            'htmlContent' => $this->html
        );

        $config = \Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $api_key);

        $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(new \GuzzleHttp\Client(), $config);

        $sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail([
            'subject' => $this->subject,
            'sender' => ['name' => $this->from_name, 'email' => $this->from_email],
            'replyTo' => ['name' => $this->from_name, 'email' => $this->from_email],
            'to' => $to_list,
            'htmlContent' => $this->html,
        ]);

        try {
            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);

            $msg_id = $result->getMessageId();
            if ($msg_id) {
                return true;
            }
        } catch (Exception $ex) {
            throw $ex;
        }

        return false;
    }

    private function _sendViaMailjet($to_list)
    {
        $api_key = env('MAILJET_API_KEY');

        if (!$api_key) {
            throw_exception("Mailjet APi Key is not set in env");
        }

        $api_secret = env('MAILJET_API_SECRET');

        if (!$api_key) {
            throw_exception("Mailjet APi Secret is not set in env");
        }

        $data = array(
            'sender' => [
                'email' => $this->from_email,
                'name' => $this->from_name
            ],
            'to' => $to_list,
            'subject' => $this->subject,
            'htmlContent' => $this->html
        );

        $mj = new \Mailjet\Client($api_key, $api_secret, true, ['version' => 'v3.1']);

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $this->from_email,
                        'Name' => $this->from_name
                    ],
                    'To' => $to_list,
                    'Subject' => $this->subject,
                    'HTMLPart' => $this->html
                ]
            ]
        ];

        // All resources are located in the Resources class
        $response = $mj->post(Resources::$Email, ['body' => $body]);

        if ($response->success()) {
            return true;
        } else {
            return false;
        }

        // dlod($response->getData());
    }
}
