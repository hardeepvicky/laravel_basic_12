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

class JobForgotPassword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public String $name, public String $email, private String $link) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $emaiHelper = new EmailHelper();

        $emaiHelper->setType(laravel_constant("msg_type.forgot_password"));

        $emaiHelper->addTo($this->email, $this->name);

        $company_name = Setting::getValue("company_name");

        if (!$company_name) {
            throw new Exception("Setting : Company Name is not set yet");
        }

        $emaiHelper->setSubject("Forgot Password");

        $html = view("mail.backend.forgot_password", [
            "company_name" => $company_name,
            "name" => $this->name,
            "link" => $this->link
        ]);

        // dd((string) $html);
        $emaiHelper->setBody((string) $html);

        $emaiHelper->send();
    }
}
