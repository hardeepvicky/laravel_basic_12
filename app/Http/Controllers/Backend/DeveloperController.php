<?php

namespace App\Http\Controllers\Backend;

use App\Acl\SectionRoutes;
use App\Helpers\ArrayHelper;
use App\Helpers\EmailHelper;
use App\Helpers\LaravelExtend;
use App\Models\AjaxLog;
use App\Models\CommandLog;
use App\Models\EmailLog;
use App\Models\ImportLog;
use App\Models\PermissionChangeLog;
use App\Models\Role;
use App\Models\SqlLog;
use App\Models\WhatsappLog;
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

    protected function beforeViewRender()
    {
        if (!parent::beforeViewRender()) {
            return false;
        }

        $index_table_info_options = [
            "show_as" => "full_datetime"
        ];

        // d($index_table_info_options);

        $this->setForView(compact("index_table_info_options"));

        return true;
    }

    protected function parseConditionValue($value, String $type, array $extra = [])
    {
        return LaravelExtend::getMongodbParseCondition($value, $type, $extra);
    }

    public function sql_logs()
    {
        $cache_key_prefix = Route::currentRouteName();

        $conditions = $this->getConditions($cache_key_prefix, [
            ["field" => "id", "type" => "", "cache" => false],
            ["field" => "route_name", "type" => "string"],
            ["field" => "request_method", "type" => "string"],
            ["field" => "url", "type" => "string"],
            ["field" => "created_at", "type" => "from_date", "view_field" => "from_date"],
            ["field" => "created_at", "type" => "to_date", "view_field" => "to_date"],
            ["field" => "auth_user_id", "type" => "int"],
            ["field" => "have_dml_query", "type" => "int"],
            ["field" => "have_heavy_query", "type" => "int"],
        ]);

        $arry_helper = new ArrayHelper($conditions);
        $condition_list = $arry_helper->convertModelSearchConditionToList();

        // dd($conditions);

        $builder = SqlLog::where($conditions);

        if (isset($condition_list['id'])) {
        } else {
            $builder->where(function ($q) {
                $q->orWhere(function ($q2) {
                    $q2->whereNotNull("route_name");
                    $q2->whereNotNull("url");
                });
            });
        }

        $cache_key_for_sort = $cache_key_prefix . "-extra-params";

        $clear_cache = request('is_sort_clear', false);

        $sort_params = $this->getRequestData($cache_key_for_sort, [
            ["key" => "sort_by", "default" => "created_at"],
            ["key" => "sort_dir", "default" => "DESC"],
        ], $clear_cache);

        $builder->orderBy($sort_params['sort_by'], $sort_params['sort_dir']);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        // d($records->toArray(), true);

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

        $cache_key_for_sort = $cache_key_prefix . "-extra-params";

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
            ["field" => "created_at", "type" => "from_date", "view_field" => "from_date"],
            ["field" => "created_at", "type" => "to_date", "view_field" => "to_date"],
        ]);

        // dd($conditions);

        $builder = CommandLog::where($conditions);
        $builder->with(["sqlLog"]);

        $cache_key_for_sort = $cache_key_prefix . "-extra-params";

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

    public function email_logs()
    {
        $cache_key_prefix = Route::currentRouteName();

        $conditions = $this->getConditions(Route::currentRouteName(), [
            ["field" => "subject", "type" => "string"],
            ["field" => "from_email", "type" => "string"],
            ["field" => "to_email", "type" => "string"],
            ["field" => "is_sent", "type" => "int"],
            ["field" => "is_sandbox", "type" => "int"],
            ["field" => "created_at", "type" => "from_date", "view_field" => "from_date"],
            ["field" => "created_at", "type" => "to_date", "view_field" => "to_date"],
        ]);

        $builder = EmailLog::where($conditions);

        $cache_key_for_sort = $cache_key_prefix . "-extra-params";

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

    public function whatsapp_logs()
    {
        $cache_key_prefix = Route::currentRouteName();

        $conditions = $this->getConditions(Route::currentRouteName(), [
            ["field" => "api_platform", "type" => "string"],
            ["field" => "template_name", "type" => "string"],
            ["field" => "template_lanugage", "type" => "string"],
            ["field" => "country_code", "type" => "string"],
            ["field" => "to_name", "type" => "string"],
            ["field" => "to_mobile", "type" => "string"],
            ["field" => "to_user_id", "type" => "int"],
            ["field" => "is_sent", "type" => "int"],
            ["field" => "is_sandbox", "type" => "int"],            
            ["field" => "created_at", "type" => "from_date", "view_field" => "from_date"],
            ["field" => "created_at", "type" => "to_date", "view_field" => "to_date"],
        ]);

        // if ($conditions)
        // {
        //     d($conditions);
        // }

        $builder = WhatsappLog::where($conditions);

        $cache_key_for_sort = $cache_key_prefix . "-extra-params";

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

    public function permission_change_logs()
    {
        $cache_key_prefix = Route::currentRouteName();

        $conditions = $this->getConditions(Route::currentRouteName(), [
            ["field" => "type", "type" => "string_equal"],
            ["field" => "section_name", "type" => "string_equal"],
            ["field" => "role_id", "type" => "string_equal"],
            ["field" => "created_at", "type" => "from_date", "view_field" => "from_date"],
            ["field" => "created_at", "type" => "to_date", "view_field" => "to_date"],
        ]);

        $builder = PermissionChangeLog::where($conditions);

        $cache_key_for_sort = $cache_key_prefix . "-extra-params";

        $clear_cache = request('is_sort_clear', false);

        $sort_params = $this->getRequestData($cache_key_for_sort, [
            ["key" => "sort_by", "default" => "created_at"],
            ["key" => "sort_dir", "default" => "DESC"],
        ], $clear_cache);

        $builder->orderBy($sort_params['sort_by'], $sort_params['sort_dir']);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        $section_list = SectionRoutes::getList();

        $role_list = Role::getList();

        $type_list = [
            "NEW" => "New",
            "DELETE" => "Deleted",
        ];

        $this->setForView(compact("role_list", "section_list", "type_list", "records"));

        return $this->viewIndex(__FUNCTION__);
    }

    public function import_logs()
    {
        $cache_key_prefix = Route::currentRouteName();

        $conditions = $this->getConditions(Route::currentRouteName(), [
            ["field" => "type", "type" => "string_equal"],
            ["field" => "have_error", "type" => "int"],
            ["field" => "created_at", "type" => "from_date", "view_field" => "from_date"],
            ["field" => "created_at", "type" => "to_date", "view_field" => "to_date"],
        ]);

        $builder = ImportLog::where($conditions);

        $cache_key_for_sort = $cache_key_prefix . "-extra-params";

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
