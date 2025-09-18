<?php

namespace App\Http\Controllers;

use App\Helpers\DateUtility;
use App\Helpers\LaravelExtend;
use App\Http\Controllers\WebController;
use App\Models\ImportLog;
use Exception;
use Illuminate\Http\Request;
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

    public function get_date_time_for_timezone(Request $request)
    {
        $datetime = $request->get("datetime");

        $timezone = $request->get("timezone");

        $dt = DateUtility::getDateObj($datetime, DateUtility::$login_user_time_zone);

        if ($timezone)
        {
            $dt->setTimezone(new \DateTimeZone($timezone));
        }

        $new_datetime = $dt->format(DateUtility::DATETIME_OUT_FORMAT);
        
        return $new_datetime;
    }
}
