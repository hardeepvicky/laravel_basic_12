<?php

namespace App\Jobs\Backend;

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


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public String $name, public String $email) {}

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
        ]);

        // echo $html;
        // exit;

        $emaiHelper->setBody((string) $html);

        $emaiHelper->send();
    }
}
