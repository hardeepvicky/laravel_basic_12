<?php

namespace App\Jobs\Backend;

use App\Exceptions\UserException;
use App\Helpers\EmailHelper;
use App\Models\EmailTemplate;
use App\Models\Setting;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class JobForgotPassword
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public User $user, private String $link) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $template = EmailTemplate::where("name", "forgot_password")->first();

        if (!$template)
        {
            throw new UserException("Email Template not found : forgot_password", UserException::ERROR_USER_DATA_WRONG);
        }

        if (!$template->is_active)
        {
            return;
        }

        $emaiHelper = new EmailHelper();

        $emaiHelper->setType($template->name);

        $emaiHelper->addTo($this->user->email, $this->user->full_name, $this->user->id);

        $ret = $template->getBodyAndSubject([
            "link" => $this->link
        ], $this->user);

        $emaiHelper->setSubject($ret['subject']);

        $emaiHelper->setBody($ret['body']);

        $result = $emaiHelper->setSandbox(false)->send();

        if ($result['fail'] > 0 || !empty($result['errors']))
        {
            $msg = "Total : " . $result['total'];
            $msg = ", Success : " . $result['success'];
            $msg .= ", Fail : " . $result['fail'];
            Log::error($msg, $result['errors']);
        }
    }
}
