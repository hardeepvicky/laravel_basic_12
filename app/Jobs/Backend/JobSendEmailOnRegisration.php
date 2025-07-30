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

class JobSendEmailOnRegisration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public User $user) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $template = EmailTemplate::where("name", "registeration")->first();

        if (!$template)
        {
            throw new UserException("Email Template not found : registeration", UserException::ERROR_USER_DATA_WRONG);
        }

        if (!$template->is_active)
        {
            return;
        }

        $emaiHelper = new EmailHelper();

        $emaiHelper->setType($template->name);

        $emaiHelper->addTo($this->user->email, $this->user->full_name, $this->user->id);

        $ret = $template->getBodyAndSubject([], $this->user);

        $emaiHelper->setSubject($ret['subject']);

        $emaiHelper->setBody($ret['body']);

        $result = $emaiHelper->setSandbox(false)->send();

        if ($result['fail'] > 0 || !empty($result['errors']))
        {
            $msg = "Success : " . $result['success'];
            $msg .= ", Fail : " . $result['fail'];
            $msg .= ", Errors : " . implode(",", array_get_only_scaler_values($result['errors']));

            throw new UserException($msg, UserException::ERROR_API_FAIL);
        }
    }
}
