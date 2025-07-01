<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;

class FrontendController extends WebController
{
    protected function beforeViewRender()
    {
        if (!parent::beforeViewRender()) {
            return false;
        }

        $request = request();

        if ($request->ajax()) {
            $this->layout = "frontend.layouts.ajax";
        } else {
            $this->layout = "frontend.layouts.main";
        }

        $this->viewPrefix = "frontend." . $this->getControllerName();

        $request = request();

        $current_route_name = Route::currentRouteName();

        $partial_path = "frontend.partials";

        $this->setForView(compact("current_route_name", "partial_path"));

        return true;
    }

    public function index()
    {
        return $this->view("index");
    }
}
