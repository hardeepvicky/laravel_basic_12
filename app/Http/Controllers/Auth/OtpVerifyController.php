<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\UserException;
use App\Helpers\DateUtility;
use App\Helpers\LaravelExtend;
use App\Http\Controllers\WebController;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $uid = trim($uid);

        if (!$uid)
        {
            throw new UserException("Empty uid", UserException::ERROR_USER_DATA_WRONG);
        }
        
        $user = User::where("uid", $uid)->first();

        if (!$user) {
            throw new UserException("User UID is wrong : " . $uid, UserException::ERROR_USER_DATA_WRONG);
        }

        $this->setForView(compact("user"));

        return $this->view("otp_verify");
    }

    public function send_email_user_otp($uid)
    {
        $response = ["status" => 1];

        try {

            $uid = trim($uid);

            if (!$uid)
            {
                throw new UserException("Empty uid", UserException::ERROR_USER_DATA_WRONG);
            }

            $user = User::where("uid", $uid)->first();

            if (!$user) {
                throw new Exception("Wrong uid : " . $uid, UserException::ERROR_USER_DATA_WRONG);
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

            $uid = trim($uid);

            if (!$uid)
            {
                throw new UserException("Empty uid", UserException::ERROR_USER_DATA_WRONG);
            }

            $user = User::where("uid", $uid)->first();

            if (!$user) {
                throw new UserException("Wrong uid : " . $uid, UserException::ERROR_USER_DATA_WRONG);
            }

            LaravelExtend::send_whatsapp_user_otp($user);

            $response['msg'] = "OTP sent on " . str_protected($user->whatsapp_mobile);
        } catch (Exception $ex) {
            $response['status'] = 0;
            $response['msg'] = $ex->getMessage();
        }

        return $this->responseJson($response);
    }

    public function ajax_otp_verify(Request $request)
    {
        $response = ["status" => 0, "msg" => "unknown"];

        try {
            $data = $request->validate([
                'user_id' => ['required'],
                'otp' => ['required'],
            ]);

            $user = User::findOrFail($data['user_id']);

            if (!$user) {
                throw new UserException("Invalid User ID : " . $data['user_id']);
            }

            $diff = DateUtility::diff(null, $user->otp_sent_datetime, DateUtility::MINTUES);

            if ($diff > 10) {
                throw new UserException("OTP has expired", UserException::ERROR_USER_ACTION_RESTRICT);
            }

            if ($data['otp'] != $user->otp) {
                throw new UserException("Wrong OTP", UserException::ERROR_USER_DATA_WRONG);
            }

            $user->email_verified_at = date("Y-m-d H:i:s");
            $user->is_active = 1;

            if (!$user->save()) {
                throw new UserException("Fail To Save User");
            }

            Auth::login($user, true);
            
            $request->session()->regenerate();

            $response['status'] = 1;
        } catch (Exception $ex) {
            $response['msg'] = $ex->getMessage();
        }

        return $response;
    }
}
