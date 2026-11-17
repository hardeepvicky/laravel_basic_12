<?php

namespace App\Http\Controllers;

use App\Helpers\LaravelExtend;
use App\Http\Controllers\WebController;
use App\Models\ImportLog;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class HomeController extends WebController
{
    public function index()
    {
        if (Auth::check()) {
            return Redirect::route("admin.dashboard");
        } else {
            return Redirect::route("login");
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

    public function import_log_status($import_log_id)
    {
        $response = ["status" => 1, "msg" => "", "status_info" => ""];

        try
        {
            $model = ImportLog::findOrFail($import_log_id);

            $response['status_info'] = $model->status_info;
        }
        catch(Exception $ex)
        {
            $response['status'] = 0;
            $response['msg'] = get_exception_display_message($ex);
        }

        return $this->responseJson($response);
    }
}
