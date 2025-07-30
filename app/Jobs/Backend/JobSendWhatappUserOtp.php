<?php

namespace App\Jobs\Backend;

use App\Exceptions\UserException;
use App\Helpers\EmailHelper;
use App\Helpers\LaravelExtend;
use App\Helpers\WhatsappHelper;
use App\Models\Setting;
use App\Models\WhatsappTemplate;
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
    public function __construct(public String $name, public String $mobile, public String $country_code, public String $otp, public $user_id = null) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $template = WhatsappTemplate::where("name", WhatsappTemplate::TEMPLATE_OTP)->first();
        if (!$template)
        {
            throw new UserException("Template not found : " . WhatsappTemplate::TEMPLATE_OTP, UserException::ERROR_USER_DATA_WRONG);
        }

        if (!$template->is_active)
        {
            return;
        }

        $whatsappHelper = new WhatsappHelper();
        $whatsappHelper->setTemplate($template->name, $template->language)
            ->setBodyValues([$this->otp])
            ->setButtonValues(["1" => [$this->otp]])
            ->setCountryCode("+" . $this->country_code)
            ->addTo($this->mobile, $this->name, $this->user_id)
        ;

        $result = $whatsappHelper->send();

        if ($result['fail'] > 0 || !empty($result['errors']))
        {
            $msg = "Success : " . $result['success'];
            $msg .= ", Fail : " . $result['fail'];
            $msg .= ", Errors : " . implode(",", array_get_only_scaler_values($result['errors']));

            throw new UserException($msg, UserException::ERROR_API_FAIL);
        }
    }
}
