<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\LaravelExtend;
use App\Http\Controllers\WebController;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Rules\ReCaptcha;
use Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Validation\ValidationException;

class LoginController extends WebController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function showLoginForm(Request $request)
    {
        return $this->view("login");
    }

    public function sign_in_form(Request $request)
    {
        return $this->view("sign_in");
    }

    protected function beforeViewRender()
    {
        if (!parent::beforeViewRender()) {
            return false;
        }

        $this->routePrefix = "";

        $this->viewPrefix = "auth";

        $this->layout = "auth.layouts.login";
    }

    protected function authenticated(Request $request, $user)
    {
        return redirect($this->redirectTo);
    }

    public function auto_login(Request $request)
    {
        $ip = $request->ip();

        if ($ip) {
            $user = User::whereHas("userAllowIP", function ($q) use ($ip) {
                $q->where("ip", $ip);
            })->first();

            if ($user) {
                $credentials =  [
                    "email" => $user->email,
                    "password" => $user->plain_password
                ];

                $result = $this->guard()->attempt(
                    $credentials,
                    false
                );

                if ($result) {
                    if ($request->hasSession()) {
                        $request->session()->put('auth.password_confirmed_at', time());
                    }

                    return $this->sendLoginResponse($request);
                }
            } else {
                return back()->with("fail", "You are not allow to auto login");
            }
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function check_before_login(Request $request)
    {
        $guard = $this->guard();

        if ($request->get("user_type") == User::TYPE_EXTERNAL) {
            // $request->validate([
            //     'g-recaptcha-response' => ['required', new ReCaptcha]
            // ], [
            //     "g-recaptcha-response.required" => "Google Capcha verification required"
            // ]);
        }

        $credentials = $this->credentials($request);

        // d($credentials, true);

        if (!$guard->once($credentials)) {

            throw ValidationException::withMessages([
                $this->username() => [trans('auth.failed')],
            ]);
        }

        $user = $guard->getUser();

        // dd($user);

        if ($user->isRquireOTPVerificationOnLogin()) {
            return redirect(route("otp_verify", ["uid" => $user->uid]));
        } else {
            return $this->login($request);
        }
    }

    /**
     * Get the credentials to be used by the controller.
     * Overrides the default credentials method to handle both email and mobile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $login = $request->input($this->username());

        if ($request->get("user_type") == User::TYPE_EXTERNAL) {
            $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
        } else {
            $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        }

        return [
            $field => $login,
            'password' => $request->input('password'),
            "type" => $request->input("user_type")
        ];
    }

    public function username()
    {
        return "login_field";
    }
}
