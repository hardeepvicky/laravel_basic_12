<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\DateUtility;
use App\Models\Tag;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends BackendController
{
    public String $routePrefix = "dashbaord";

    public function index()
    {
        $view_name = "admin";

        $msg = "Comming Soon";

        $this->setForView(compact("view_name", "msg"));

        return $this->view($view_name);
    }
}
