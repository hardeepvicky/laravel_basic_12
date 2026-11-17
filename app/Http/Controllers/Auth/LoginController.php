<?php

namespace App\Http\Controllers\Auth;

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

    protected $user;

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
        $user->login_timezone = $request->get("timezone");
        $user->save();

        return redirect($this->redirectTo);
    }

    public function check_before_login(Request $request)
    {
        $guard = $this->guard();

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
            $field = 'mobile';
        } else {
            $field = 'email';
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
            return redirect('/');
        }
    }
}
