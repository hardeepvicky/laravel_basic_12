<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\LaravelExtend;
use App\Models\EmailLog;
use App\Models\SqlLog;
use Illuminate\Support\Facades\Route;

class DeveloperController extends BackendController
{
    public String $routePrefix = "developer";

    public function laravel_routes_index()
    {
        $routes = LaravelExtend::getRoutes();

        $this->setForView(compact("routes"));

        return $this->view(__FUNCTION__);
    }

    public function sql_log()
    {
        $cache_key_prefix = Route::currentRouteName();

        $conditions = $this->getConditions($cache_key_prefix . "." . __FUNCTION__, [
            ["field" => "route_name_or_url", "type" => "string", "view_field" => "route_name_or_url"],
            ["field" => "created_at", "type" => "from_date", "view_field" => "from_date"],
            ["field" => "created_at", "type" => "to_date", "view_field" => "to_date"],
        ]);

        // dd($conditions);

        $builder = SqlLog::where($conditions);

        $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

        $clear_cache = request('is_sort_clear', false);

        $sort_params = $this->getRequestData($cache_key_for_sort, [
            ["key" => "sort_by", "default" => "id"],
            ["key" => "sort_dir", "default" => "DESC"],
        ], $clear_cache);

        $builder->orderBy($sort_params['sort_by'], $sort_params['sort_dir']);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        // d($this->getQueryLog());

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    public function email_log()
    {
        $conditions = $this->getConditions(Route::currentRouteName(), [
            ["field" => "from_email", "type" => "string", "view_field" => "from_email"],
            ["field" => "to_email", "type" => "string", "view_field" => "to_email"],
            ["field" => "created", "type" => "from_date", "view_field" => "from_date"],
            ["field" => "created", "type" => "to_date", "view_field" => "to_date"],
        ]);

        $cache_key_prefix = Route::currentRouteName();

        $records = $this->getPaginagteRecords(EmailLog::where($conditions), $cache_key_prefix);

        //dd($records);

        $this->setForView(compact("records"));

        return $this->view(__FUNCTION__);
    }

    // public function import_logs()
    // {
    //     $modelName = ImportLog::class;

    //     $conditions = $this->getConditions(Route::currentRouteName(), [
    //         ["field" => "type", "type" => "string", "view_field" => "type"],
    //         ["field" => "is_fully_run", "type" => "int", "view_field" => "is_fully_run"],
    //         ["field" => "have_error", "type" => "int", "view_field" => "have_error"],
    //         ["field" => "created", "type" => "from_date", "view_field" => "from_date"],
    //         ["field" => "created", "type" => "to_date", "view_field" => "to_date"],
    //     ]);

    //     $records = $this->getPaginagteRecords($modelName::where($conditions));

    //     //dd($records);

    //     $userList = User::getListCache();

    //     $this->setForView(compact("records", "modelName", "userList"));

    //     $view_name = "import_logs";
    //     if (request()->ajax()) {
    //         $view_name = "import_logs_index_table";
    //     }

    //     return $this->view($view_name);
    // }
}
