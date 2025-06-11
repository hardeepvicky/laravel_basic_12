<?php

namespace App\Jobs\backend;

use App\Helpers\EmailHelper;
use App\Models\Setting;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobSendEmailOnRegisration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected String $name, $email, $otp, $verify_screen_link;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(String $name, String $email, String $otp, String $verify_screen_link)
    {
        $this->name = $name;
        $this->email = $email;
        $this->otp = $otp;
        $this->verify_screen_link = $verify_screen_link;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $emaiHelper = new EmailHelper(EmailHelper::EMAIL_VIA_MAILJET);

        $emaiHelper->setType(config("constant.email_type.registration"));

        $emaiHelper->setTo($this->email, $this->name);

        $company_name = Setting::getValue("company_name");

        if (!$company_name) {
            throw new Exception("Setting : Company Name is not set yet");
        }

        $company_email = Setting::getValue("company_email");

        if (!$company_email) {
            throw new Exception("Setting : Company Email is not set yet");
        }

        $emaiHelper->setSubject("Registeration");

        $html = view("mail.backend.registration", [
            "company_name" => $company_name,
            "company_email" => $company_email,
            "name" => $this->name,
            "otp" => $this->otp,
            "verify_link" => $this->verify_screen_link
        ]);

        $emaiHelper->setBody((string) $html);

        $emaiHelper->send();
    }
}
