<?php

namespace App\Http\Controllers\Auth;

<<<<<<< HEAD
use App\Http\Controllers\WebController;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
=======
use App\Helpers\LaravelExtend;
use App\Http\Controllers\WebController;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Rules\ReCaptcha;
use Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
>>>>>>> base_v3

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

<<<<<<< HEAD
=======
    protected $user;

>>>>>>> base_v3
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
<<<<<<< HEAD

        // $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
=======
    }

    public function showLoginForm(Request $request)
>>>>>>> base_v3
    {
        return $this->view("login");
    }

<<<<<<< HEAD
    protected function beforeViewRender()
    {
        if (!parent::beforeViewRender())
        {
=======
    public function sign_in_form(Request $request)
    {
        return $this->view("sign_in");
    }

    protected function beforeViewRender()
    {
        if (!parent::beforeViewRender()) {
>>>>>>> base_v3
            return false;
        }

        $this->routePrefix = "";

        $this->viewPrefix = "auth";

        $this->layout = "auth.layouts.login";
    }
<<<<<<< HEAD
}
=======

    protected function authenticated(Request $request, $user)
    {
        $user->login_timezone = $request->get("timezone");
        $user->save();

        return redirect($this->redirectTo);
    }

    public function auto_login(Request $request)
    {
        $ip = $request->ip();

        if ($ip) 
        {
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
                else
                {
                    $this->incrementLoginAttempts($request);

                    return $this->sendFailedLoginResponse($request);
                }
            } 
            else 
            {
                return back()->with("fail", "You are not allow to auto login");
            }
        }
        else
        {
            return back()->with("fail", "could not get ip address");
        }
    }

    public function check_before_login(Request $request)
    {
        $guard = $this->guard();

        if ($request->get("user_type") == User::TYPE_EXTERNAL) {
            $request->validate([
                'g-recaptcha-response' => ['required', new ReCaptcha]
            ], [
                "g-recaptcha-response.required" => "Google Capcha verification required"
            ]);
        }

        if (!$request->get("timezone"))
        {
            return back()->with("fail", "timezone is required");
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
            "type" => $request->input("user_type"),
        ];
    }

    public function username()
    {
        return "login_field";
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->user = $this->guard()->getUser();

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        if (isset($this->user->type) && $this->user->type == User::TYPE_INTERNAL)
        {
            return redirect('/login');
        }
        else
        {
            return redirect('/signin');
        }
    }
}
>>>>>>> base_v3
