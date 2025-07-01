<?php

namespace App\Http\Controllers;

use App\Helpers\LaravelExtend;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class HomeController extends WebController
{
    public function index()
    {
        if (Auth::check()) {
            return Redirect::route("dashboard");
        } else {

            $route_name = "login";

            if (!LaravelExtend::isRequestFromLocal()) {
                $route_name = "sign_in";
            }

            return Redirect::route($route_name);
        }
    }

    public function test()
    {
        return view("backend.test");
    }

    public function theme()
    {
        return view("backend.theme");
    }

    public function developer_components()
    {
        return view("backend.developer_components");
    }
}
