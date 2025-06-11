<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\DateUtility;
use App\Helpers\LaravelExtend;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    public function register()
    {
        $request = request();

        $recaptcha_response = $request->input('g-recaptcha-response');

        if (is_null($recaptcha_response)) {
            return redirect()->back()->with('fail', 'Please Complete the Recaptcha to proceed');
        }

        $url = "https://www.google.com/recaptcha/api/siteverify";

        $body = [
            'secret' => config('services.recaptcha.secret'),
            'response' => $recaptcha_response,
            'remoteip' => $request->ip() //anonymize the ip to be GDPR compliant. Otherwise just pass the default ip address
        ];

        $response = Http::asForm()->post($url, $body);

        $result = json_decode($response);

        if ($response->successful() && $result->success == true) {

            $data = request()->all();

            $this->validator($data)->validate();

            $user = User::create([
                'type' => config("constant.user_type.backend"),
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                "uid" => User::generateUID()
            ]);


            LaravelExtend::send_email_otp($user);

            return redirect($this->_getVerifyScreenLink($user));
        } else {
            return redirect()->back()->with('fail', 'Please Complete the Recaptcha Again to proceed');
        }

        return null;
    }

    public function ajax_send_email_otp($user_id)
    {
        $response = ["status" => 0, "msg" => "unknown"];

        try {
            $user = User::findOrFail($user_id);

            LaravelExtend::send_email_otp($user);

            $response['status'] = 1;
        } catch (Exception $ex) {
            $response['msg'] = $ex->getMessage();
        }

        return $response;
    }

    public function email_otp_verify($uid)
    {
        $user = User::where("uid", $uid)->first();

        if (!$user) {
            return redirect()->route("login")->with('fail', "Invalid UID");
        }

        if ($user->email_verified_at) {
            return redirect(route("home"));
        }

        if ($user->dont_send_email) {
            return redirect()->route("login")->with('fail', "We can't send email. Beacuase Don't send email is enabled");
        }

        $company_name = Setting::getValue("company_name");

        return view("auth.email_otp_verify", [
            "user_id" => $user->id,
            "user_email" => $user->email,
            "company_name" => $company_name,
            "redirect_to" => "/admin/dashboard"
        ]);
    }

    public function ajax_email_otp_verify()
    {
        $response = ["status" => 0, "msg" => "unknown"];

        try {
            $data = request()->all();

            Validator::make($data, [
                'user_id' => ['required'],
                'otp' => ['required'],
            ])->validate();

            $user = User::findOrFail($data['user_id']);

            if (!$user) {
                throw_exception("Worng User ID");
            }

            $diff = DateUtility::diff(date(DateUtility::DATETIME_FORMAT), $user->otp_sent_datetime, DateUtility::MINTUES);

            if ($diff > 15) {
                throw new Exception("OTP has expired");
            }

            if ($data['otp'] != $user->email_otp) {
                throw new Exception("Wrong OTP");
            }

            $user->email_verified_at = date("Y-m-d H:i:s");
            $user->is_active = 1;

            if (!$user->save()) {
                throw_exception("Fail To Save User");
            }

            $this->guard()->login($user);

            $response['status'] = 1;
        } catch (Exception $ex) {
            $response['msg'] = $ex->getMessage();
        }

        return $response;
    }


    public function forgot_password(Request $request)
    {
        if ($request->isMethod('post')) {
            try {
                $validatedData = $request->validate([
                    'email' => 'required:email',
                ]);

                $user = User::where("email", $validatedData['email'])->first();

                if (!$user) {
                    throw new \Exception("This Email is not registered with us");
                }

                if ($user->dont_send_email) {
                    throw new \Exception("We can't send email. Beacuase Don't send email is enabled");
                }

                LaravelExtend::send_forgot_password_link($user);

                return back()->with('success', 'Email Sent Successfully');
            } catch (\Exception $ex) {
                return back()->with('fail', $ex->getMessage())->withInput();
            }
        }

        $company_name = Setting::getValue("company_name");

        return view("auth.forgot_password", [
            "layout" => "auth.layouts.login",
            "company_name" => $company_name,
        ]);
    }

    public function create_password(Request $request, $uid)
    {
        $user = User::where("uid", $uid)->first();

        if (!$user) {
            die("Invalid UID");
        }

        if ($request->isMethod('post')) {
            try {
                $validatedData = $request->validate([
                    'password' => 'required|min:5|confirmed',
                ]);

                $user->password = bcrypt($validatedData['password']);

                if (!$user->save()) {
                    throw_exception("Fail To Save User");
                }

                return redirect()->route("login")->with('success', 'Password Changed Successfully');
            } catch (\Exception $ex) {
                return back()->with('fail', $ex->getMessage())->withInput();
            }
        }

        $company_name = Setting::getValue("company_name");

        return view("auth.create_password", [
            "layout" => "auth.layouts.login",
            "company_name" => $company_name,
        ]);
    }
}
