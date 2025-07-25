<?php

namespace App\Jobs\Backend;

use App\Helpers\EmailHelper;
use App\Helpers\WhatsappHelper;
use App\Models\Setting;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobSendWhatappUserOtp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public String $name, public String $mobile, public String $otp, public $user_id = null) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $whatsappHelper = new WhatsappHelper();
        $whatsappHelper->setTemplate("otp", "en_GB")
            ->setBodyValues([$this->otp])
            ->setButtonValues([
                "1" => [
                    "Copy"
                ]
            ])
            ->setCountryCode("+91")
            ->addTo($this->mobile, $this->name, $this->user_id)
            ->setSandbox(false)
        ;

        $whatsappHelper->send();
    }
}
