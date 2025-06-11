<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\WebController;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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

        // $this->middleware('guest')->except('logout');
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
        if (!$user->dont_send_email && is_null($user->email_verified_at)) {
            if (!$user->uid) {
                throw_exception("UID is not set");
            }

            return redirect(route("email_otp_verify", ["uid" => $user->uid]));
        }
    }
}
