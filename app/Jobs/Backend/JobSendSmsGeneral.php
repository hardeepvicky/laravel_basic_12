<?php

namespace App\Jobs\Backend;

use App\Exceptions\UserException;
use App\Helpers\SmsHelper;
use App\Models\SmsTemplate;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class JobSendSmsGeneral implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels, Queueable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public int $port, public String $name, public String $mobile, public String $country_code, public string $msg, public $user_id = null) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $template = SmsTemplate::where("name", "general")->first();
        if (!$template)
        {
            throw new UserException("Template not found : general", UserException::ERROR_USER_DATA_WRONG);
        }

        if (!$template->is_active)
        {
            return;
        }

        $smsHelper = new SmsHelper();
        $smsHelper->setPort($this->port)
            ->setTemplate($template->template_name, $template->language)
            ->setCountryCode("+" . $this->country_code)
            ->setTo($this->mobile, $this->name, $this->user_id)
            ->setMessage($this->msg)
        ;

        $result = $smsHelper->setSandbox(false)->send();

        if ($result['fail'] > 0 || !empty($result['errors']))
        {
            $msg = "Total : " . $result['total'];
            $msg = ", Success : " . $result['success'];
            $msg .= ", Fail : " . $result['fail'];
            Log::error($msg, $result['errors']);
        }

        return $result;
    }
}
