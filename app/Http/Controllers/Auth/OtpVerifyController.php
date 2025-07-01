<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\UserException;
use App\Helpers\LaravelExtend;
use App\Http\Controllers\WebController;
use App\Models\User;
use Exception;

class OtpVerifyController extends WebController
{
    protected function beforeViewRender()
    {
        if (!parent::beforeViewRender()) {
            return false;
        }

        $this->routePrefix = "";

        $this->viewPrefix = "auth";

        $this->layout = "auth.layouts.login";
    }

    public function index($uid)
    {
        $user = User::where("uid", $uid)->first();

        if (!$user) {
            throw new UserException("User UID is wrong : " . $uid);
        }

        $this->setForView(compact("user"));

        return $this->view("otp_verify");
    }

    public function send_email_user_otp($uid)
    {
        $response = ["status" => 1];

        try {
            $user = User::where("uid", $uid)->first();

            if (!$user) {
                throw new Exception("Wrong uid : " + $uid);
            }

            LaravelExtend::send_email_user_otp($user);

            $response['msg'] = "OTP sent on " . str_protected($user->email);
        } catch (Exception $ex) {
            $response['status'] = 0;
            $response['msg'] = $ex->getMessage();
        }

        return $this->responseJson($response);
    }

    public function send_whatsapp_user_otp($uid)
    {
        $response = ["status" => 1];

        try {
            $user = User::where("uid", $uid)->first();

            if (!$user) {
                throw new Exception("Wrong uid : " + $uid);
            }

            LaravelExtend::send_email_user_otp($user);

            $response['msg'] = "OTP sent on " . str_protected($user->email);
        } catch (Exception $ex) {
            $response['status'] = 0;
            $response['msg'] = $ex->getMessage();
        }

        return $this->responseJson($response);
    }
}
