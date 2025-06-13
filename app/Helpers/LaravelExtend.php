<?php

namespace App\Helpers;

use App\Jobs\Backend\JobForgotPassword;
use App\Jobs\Backend\JobSendEmailOnLogin;
use App\Jobs\Backend\JobSendEmailOnRegisration;
use App\Models\User;
use Exception;

class LaravelExtend
{
    public static function getRoutes()
    {
        $routeCollections = \Illuminate\Support\Facades\Route::getRoutes();

        $routes = [];
        foreach ($routeCollections as $routeObj) {

            $route = [
                'url' => $routeObj->uri,
                'route_name' => $routeObj->getName(),
                "action" => $routeObj->getActionName(),
            ];

            $routes[] = $route;
        }

        //d($routes); exit;

        return $routes;
    }

    public static function send_email_login_otp(User $user)
    {
        $user->otp = Util::getRandomString(4, "1234567890");
        $user->otp_sent_datetime = date("Y-m-d H:i:s");

        if (!$user->save()) {
            throw_exception("User Save Failed");
        }

        $link =  config("url") . "/email_otp_verify/" . $user->uid;

        $job = new JobSendEmailOnLogin($user->name, $user->email, $user->otp, $link);

        dispatch($job);
    }

    public static function send_email_on_registeration(User $user)
    {
        $job = new JobSendEmailOnRegisration($user->name, $user->email);

        dispatch($job);
    }

    public static function send_forgot_password_link(User $user)
    {
        if (!$user->uid) {
            throw new Exception("UId is not generated");
        }

        $link =  config("url") . "/create_password/" . $user->uid;

        $job = new JobForgotPassword($user->name, $user->email, $link);

        dispatch($job);
    }
}
