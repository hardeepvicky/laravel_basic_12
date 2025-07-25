<?php

namespace App\Listeners;

use App\Events\UserInsert;
use App\Helpers\LaravelExtend;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Session;

class UserInsertListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserInsert $event): void
    {
        $success_msgs = [];
        if (!$event->user->dont_send_email)
        {
            LaravelExtend::send_email_on_registeration($event->user);
            $success_msgs[] = "Registration welcome email is sent";

            LaravelExtend::send_email_user_otp($event->user);
            $success_msgs[] = "OTP email is sent";
        }

        if ($event->user->whatsapp_mobile)
        {
            // LaravelExtend::send_whatsapp_user_otp($event->user);
        }

        Session::flash("success", $success_msgs);
    }
}
