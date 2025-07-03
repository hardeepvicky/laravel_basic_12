<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\LaravelExtend;
use App\Models\AjaxLog;
use App\Models\CommandLog;
use App\Models\EmailLog;
use App\Models\SqlLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

    protected function parseConditionValue($value, String $type)
    {
        $data_type = gettype($value);

        $type = strtolower(trim($type));

        if (in_array($type, ["string", "date", "from_date", "to_date", "datetime", "from_datetime", "to_datetime"])) {
            if ($data_type != "string") {
                throw_exception("input value is not typeof string");
            }
        }

        $parse_value = null;
        $op = '=';

        switch ($type) {
            case "string":
                $parse_value = "%" . (string) $value . "%";
                $op = 'LIKE';
                break;

            case "string_equal":
                $parse_value = (string) $value;
                break;

            case "date":
                $parse_value = Carbon::parse($value);
                break;

            case "from_date":
                $parse_value = Carbon::parse($value);
                $op = '>=';
                break;

            case "to_date":
                $parse_value = Carbon::parse($value);
                $parse_value->addHours(23.99);
                $op = '<=';
                break;

            case "from_datetime":
                $parse_value = Carbon::parse($value);
                $op = '>=';
                break;

            case "to_datetime":
                $parse_value = Carbon::parse($value);
                $op = '<=';
                break;

            case "int":
                $parse_value = (int) $value;
                break;

            case "from_int":
                $parse_value = (int) $value;
                $op = '>=';
                break;

            case "to_int":
                $parse_value = (int) $value;
                $op = '<=';
                break;

            case "float":
                $parse_value = (float) $value;
                break;

            case "from_float":
                $parse_value = (float) $value;
                $op = '>=';
                break;

            case "to_float":
                $parse_value = (float) $value;
                $op = '<=';
                break;

            case "bool":
            case "boolean":
                $parse_value = (bool) $value;
                break;

            default:
                $parse_value = $value;
                break;
        }

        return [
            "value" => $parse_value,
            "op" => $op
        ];
    }

    public function sql_logs()
    {
        $cache_key_prefix = Route::currentRouteName();

        $conditions = $this->getConditions($cache_key_prefix, [
            ["field" => "route_name_or_url", "type" => "string", "view_field" => "route_name_or_url"],
            ["field" => "created_at", "type" => "from_date", "view_field" => "from_date"],
            ["field" => "created_at", "type" => "to_date", "view_field" => "to_date"],
            ["field" => "have_dml_query", "type" => "int"],
            ["field" => "have_heavy_query", "type" => "int"],
        ]);

        // dd($conditions);

        $builder = SqlLog::where($conditions)->with("cacheLog");

        $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

        $clear_cache = request('is_sort_clear', false);

        $sort_params = $this->getRequestData($cache_key_for_sort, [
            ["key" => "sort_by", "default" => "created_at"],
            ["key" => "sort_dir", "default" => "DESC"],
        ], $clear_cache);

        $builder->orderBy($sort_params['sort_by'], $sort_params['sort_dir']);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        // d($records[0]->cacheLog, true);

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    public function email_logs()
    {
        $cache_key_prefix = Route::currentRouteName();

        $conditions = $this->getConditions(Route::currentRouteName(), [
            ["field" => "from_email", "type" => "string", "view_field" => "from_email"],
            ["field" => "to_email", "type" => "string", "view_field" => "to_email"],
            ["field" => "created", "type" => "from_date", "view_field" => "from_date"],
            ["field" => "created", "type" => "to_date", "view_field" => "to_date"],
        ]);

        $builder = EmailLog::where($conditions);

        $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

        $clear_cache = request('is_sort_clear', false);

        $sort_params = $this->getRequestData($cache_key_for_sort, [
            ["key" => "sort_by", "default" => "created_at"],
            ["key" => "sort_dir", "default" => "DESC"],
        ], $clear_cache);

        $builder->orderBy($sort_params['sort_by'], $sort_params['sort_dir']);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        //dd($records);

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    public function ajax_logs()
    {
        $cache_key_prefix = Route::currentRouteName();

        $conditions = $this->getConditions(Route::currentRouteName(), [
            ["field" => "route_name_or_url", "type" => "string", "view_field" => "route_name_or_url"],
            ["field" => "sql_model.have_dml_query", "type" => "bool", "view_field" => "have_dml_query"],
            ["field" => "sql_model.have_heavy_query", "type" => "bool", "view_field" => "have_heavy_query"],
            ["field" => "created_at", "type" => "from_date", "view_field" => "from_date"],
            ["field" => "created_at", "type" => "to_date", "view_field" => "to_date"],
        ]);

        $builder = AjaxLog::where($conditions);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        // d($records->toArray(), true);

        $builder = AjaxLog::where($conditions);

        $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

        $clear_cache = request('is_sort_clear', false);

        $sort_params = $this->getRequestData($cache_key_for_sort, [
            ["key" => "sort_by", "default" => "created_at"],
            ["key" => "sort_dir", "default" => "DESC"],
        ], $clear_cache);

        $builder->orderBy($sort_params['sort_by'], $sort_params['sort_dir']);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        //dd($records);

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    public function command_logs()
    {
        $cache_key_prefix = Route::currentRouteName();

        $conditions = $this->getConditions($cache_key_prefix, [
            ["field" => "command", "type" => "string"],
            ["field" => "description", "type" => "string"],
            ["field" => "have_dml_query", "type" => "bool", "view_field" => "have_dml_query"],
            ["field" => "have_heavy_query", "type" => "bool", "view_field" => "have_heavy_query"],
            ["field" => "created_at", "type" => "from_date", "view_field" => "from_date"],
            ["field" => "created_at", "type" => "to_date", "view_field" => "to_date"],
        ]);

        // dd($conditions);

        $builder = CommandLog::where($conditions);

        $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

        $clear_cache = request('is_sort_clear', false);

        $sort_params = $this->getRequestData($cache_key_for_sort, [
            ["key" => "sort_by", "default" => "created_at"],
            ["key" => "sort_dir", "default" => "DESC"],
        ], $clear_cache);

        $builder->orderBy($sort_params['sort_by'], $sort_params['sort_dir']);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }
}
