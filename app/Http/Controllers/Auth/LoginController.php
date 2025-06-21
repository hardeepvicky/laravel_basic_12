<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\UserException;
use App\Helpers\LaravelExtend;
use App\Http\Controllers\WebController;
use App\Providers\RouteServiceProvider;
use Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use ReflectionClass;
use ReflectionMethod;

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

    public function showLoginForm()
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
        return redirect($this->redirectTo);
    }

    public function check_before_login(Request $request)
    {
        $guard = $this->guard();

        if (!$guard->once($request->only(["email", "password"]))) {

            //login failed
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.failed')],
            ]);
        }

        $user = $guard->getUser();
        if ($user->isRquireOTPVerificationOnLogin()) {
            return redirect(route("otp_verify", ["uid" => $user->uid]));
        } else {
            return $this->login($request);
        }
    }
}
