<?php

namespace App\Http\Controllers;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\DateUtility;
use App\Helpers\LaravelExtend;
use App\Models\BaseModelInterface;
use App\Models\Setting;
use App\Traits\CacheTrait;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WebController extends Controller
{
    use CacheTrait;

    /**
     * variable require for view
     */
    protected String $routePrefix, $viewPrefix, $tableName, $layout;

    protected $modelClass;

    protected int $paginationLimit = 10;

    /**
     * variable to store data which will pass to view
     */
    private $data = [];

    public function __construct()
    {
        if (isset($this->modelClass)) {
            $model = new $this->modelClass();

            $this->tableName = $model->getTable();
        }

        $auth_user = Auth::user();

        if ($auth_user)
        {
            if ($auth_user->login_timezone)
            {
                // LaravelExtend::addWarning("User", "Timezone is $auth_user->login_timezone", "Security");
                DateUtility::$login_user_time_zone = new \DateTimeZone($auth_user->login_timezone);
            }
            else
            {
                LaravelExtend::addWarning("", "Users's timezone could not found, so datetime will be shown in UTC timezone", "Login");
            }
        }
    }

    public static function getCacheKey(): string
    {
        $str = static::class;

        return static::optimizeCacheKey($str);
    }

    protected function setForView(array $array)
    {
        $this->data = array_merge($this->data, $array);
    }

    protected function beforeViewRender()
    {
        $this->viewPrefix = $this->getControllerName();

        $this->data['page_title'] = $this->getPageTitle();

        $this->data['yes_no_list'] = laravel_constant("yes_no_list");

        $this->data['success_fail_list'] = laravel_constant("success_fail_list");

        return true;
    }

    protected function view($view_name)
    {
        $this->beforeViewRender();

        if (isset($this->viewPrefix)) {
            $this->data['viewPrefix'] = $this->viewPrefix;
            $view_name = $this->viewPrefix . "." . $view_name;
        }

        if (isset($this->layout)) {
            $this->data['layout'] = $this->layout;
        }

        if (isset($this->routePrefix)) {
            $this->data['routePrefix'] = $this->routePrefix;
        }

        return view($view_name, $this->data);
    }

    protected function viewIndex($view_name)
    {
        if (request()->ajax() && !request("full_view")) {
            $view_name = "ajax_" . $view_name;
        }

        return $this->view($view_name);
    }

    protected function getControllerName(): string
    {
        $action = request()->route()->getAction();

        $action_arr = explode("@", $action['controller']);

        $controller_name = $action_arr[0];

        if (strpos($controller_name, "\\") >= 0) {
            $arr = explode("\\", $controller_name);

            if ($arr) {
                $controller_name = end($arr);
            }
        }

        $controller_name = str_replace("Controller", "", $controller_name);

        return $controller_name;
    }

    protected function getModuleName(): string
    {
        return str_space_before_every_capital_letter($this->getControllerName());
    }

    protected function getMethodName(): string
    {
        $action = request()->route()->getAction();

        $action_arr = explode("@", $action['controller']);

        if (isset($action_arr[1])) {
            $method_name = $action_arr[1];

            return $method_name;
        }

        return "";
    }

    protected function getPageTitle(): string
    {
        $method_name = $this->getMethodName();

        $sub_title = null;

        $module_name = $this->getModuleName();

        if ($method_name == "index") {
        } else if ($method_name == "show") {
            $sub_title = "View";
        } else if ($method_name == "showLoginForm") {
            $sub_title = "Login";
        } else if ($method_name == "sign_in_form") {
            $sub_title = "Sign In";
        } else {
            $sub_title = str_replace("index", "List", $method_name);

            $sub_title = str_replace("create", "Add", $sub_title);

            $sub_title = str_replace("my", "", $sub_title);

            $sub_title = str_function_name_to_human_text($sub_title);
        }

        $page_title = trim($module_name);

        if (isset($sub_title) && $sub_title) {
            $page_title .= " | " . $sub_title;
        }

        $page_title = ucwords($page_title);

        return $page_title;
    }

    protected function getRequestData($cache_prefix, array $array, bool $is_cache_clear = false)
    {
        $cache_params = [];

        if (is_string($cache_prefix) && $cache_prefix) {
            $cache_key = "request_" . $cache_prefix . "_user-" . $this->getAuthUserId();

            $cache_params = self::getCache($cache_key);

            if (!$cache_params) {
                $cache_params = [];
            }
        }

        $request_params = request()->all();

        $params = [];

        foreach ($array as $row) {
            if (!isset($row['key'])) {
                throw_exception("key in not set in argument array");
            }

            $key = $row['key'];

            if (isset($row['default'])) {
                $params[$key] = $row['default'];
            }

            if (!$is_cache_clear && isset($cache_params[$key])) {
                $params[$key] = $cache_params[$key];
            }

            if (isset($request_params[$key])) {
                $params[$key] = $cache_params[$key] = $request_params[$key];
            }
        }

        if (isset($cache_key)) {
            if ($is_cache_clear) {
                self::forgotCache($cache_key);
            } else {
                self::addCache($cache_key, $params, laravel_constant("cache_time.summary_search"));
            }
        }

        return $params;
    }

    protected function getSortBy(Model $main_model, $cache_prefix, array $array, bool $is_cache_clear = false)
    {
        $params = $this->getRequestData($cache_prefix, $array, $is_cache_clear);

        if (isset($params['sort_by']) && $params['sort_by'] && $params['sort_dir'] && $params['sort_dir'])
        {
            $sort_by = $params['sort_by'];
            $sort_dir = $params['sort_dir'];

            $arrayHelper = new ArrayHelper($params);

            UserException::checkPossibleValues($sort_dir, ["ASC", "DESC"], ["before_check_convert_string" => ["trim", "strtolower"]]);

            if (strpos($sort_by, ".") !== false)
            {
                list($relation_name, $field) = explode(".", $sort_by);

                $relation_name = trim($relation_name);
                $field = trim($field);

                if (!$relation_name)
                {
                    throw new UserException("sort_by has (.) char so system is finding relation_name, which is not found");
                }

                $relation = LaravelExtend::getRelation($main_model, $relation_name);

                if (!$relation)
                {
                    $class_name = str_class_name_without_namespace($main_model);
                    throw new UserException("relation : $relation_name not found on $class_name");
                }

                $foreign_key = $relation->getForeignKeyName();

                $relation_class = $relation->getRelated();

                $main_table_name = $main_model->getTable();
                
                if (!isset($params['relation_table_field']))
                {
                    $params['relation_table_field'] = "name";
                }

                $query_builder = $relation_class::select($params['relation_table_field'])->whereColumn('id', $main_table_name . "." . $foreign_key);

                return [
                    "sort_by" => $query_builder,
                    "sort_dir" => $sort_dir
                ];
            }
            else
            {
                $arrayHelper = new ArrayHelper($params);

                return $arrayHelper->getOnlyWhichHaveKeys(["sort_by", "sort_dir"], false, true);
            }
        }
    }

    protected function getConditions($cache_prefix, array $array, bool $return_key_value_pair_conditions = false)
    {
        $conditions = [];
        $search_variables = $cache_variables = [];

        if (is_string($cache_prefix) && $cache_prefix) {
            $cache_key = "conditions-" . $cache_prefix . "_user_" . $this->getAuthUserId();
        }

        $request_params = request()->all();

        foreach ($request_params as $k => $v) {
            if (is_string($v)) {
                $request_params[$k] = urldecode($v);
            }
        }

        if (empty($request_params) && isset($cache_key)) {
            $cache_params = self::getCache($cache_key);

            if ($cache_params) {
                $request_params = $cache_params;
            }
        }

        foreach ($array as $row) {
            if (!isset($row['field'])) {
                throw_exception("field key in not set in argument array");
            }

            if (!isset($row['type'])) {
                throw_exception("type key in not set in argument array");
            }

            $field = $row['field'];
            $view_field = $row['view_field'] ?? $row['field'];
            $search_variables[$view_field] = "";
            $value = null;
            $extra = $row['extra'] ?? [];

            if (isset($row['default']) && !empty($row['default'])) {
                $value = $row['default'];
            }

            if (isset($request_params[$view_field])) {
                $value = $request_params[$view_field];
            }

            if (!isset($value)) {
                continue;
            }

            if (is_string($value)) {
                $value = trim($value);
            }

            $search_variables[$view_field] = $value;

            if (is_array($value)) {
                if (!empty($value)) {
                    $op = "IN";

                    foreach ($value as $k => $v) {
                        if (is_string($v)) {
                            $v = trim($v);
                        }

                        $arr = $this->parseConditionValue($v, $row['type'], $extra);
                        $value[$k] = $arr['value'];
                    }

                    $parse_value = implode(",", $value);
                    if ($return_key_value_pair_conditions) {
                        $conditions[$field] = [
                            "value" => $parse_value,
                            "op" => $op,
                        ];
                    } else {
                        $conditions[] = [$field, $op, $parse_value];
                    }
                }
            } else if (strlen($value) > 0) {
                $arr = $this->parseConditionValue($value, $row['type'], $extra);

                $parse_value = $arr['value'];
                $op = $arr['op'];

                if ($return_key_value_pair_conditions) {
                    $conditions[$field] = [
                        "value" => $parse_value,
                        "op" => $op,
                    ];
                } else {
                    $conditions[] = [$field, $op, $parse_value];
                }
            }

            if (isset($row['cache'])) 
            {
                if ($row['cache'])
                {
                    $cache_variables[$view_field] = $value;
                }
            }
            else
            {
                $cache_variables[$view_field] = $value;
            }
        }

        if (isset($cache_key)) {
            self::addCache($cache_key, $cache_variables, laravel_constant("cache_time.summary_search"));
        }

        if (!isset($this->data['search'])) {
            $this->data['search'] = [];
        }

        $this->data['search'] = array_merge($this->data['search'], $search_variables);

        return $conditions;
    }

    protected function parseConditionValue($value, String $type, array $extra = [])
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
                $format = DateUtility::DATE_FORMAT;
                break;

            case "from_date":
                $format = DateUtility::DATE_FORMAT;
                $op = '>=';
                break;

            case "to_date":
                $format = DateUtility::DATE_FORMAT;
                $op = '<=';
                break;

            case "datetime":
                $format = DateUtility::DATETIME_FORMAT;
                break;

            case "from_datetime":
                $format = DateUtility::DATETIME_FORMAT;
                $op = '>=';
                break;

            case "to_datetime":
                $format = DateUtility::DATETIME_FORMAT;
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

            default:
                $parse_value = $value;
                break;
        }

        if (in_array($type, ["date", "from_date", "to_date", "datetime", "from_datetime", "to_datetime"])) {
            $parse_value = DateUtility::getDate($value, $format, DateUtility::$login_user_time_zone);
        }

        return [
            "value" => $parse_value,
            "op" => $op
        ];
    }

    protected function getPaginagteRecords($builder, String $cache_key_prefix)
    {
        $cache_key = $cache_key_prefix . "-pagination_limit";

        $params = $this->getRequestData($cache_key, [
            ["key" => "pagination_limit", "default" => $this->paginationLimit],
        ]);

        if ($params['pagination_limit'] < 1) {
            throw_exception("pagination_limit can not be less than 1");
        }

        $paginationList = [10 => 10, 20 => 20, 50 => 50, 100 => 100, 200 => 200];

        $this->setForView([
            "pagination_limit" => $params['pagination_limit'],
            "paginationList" => $paginationList
        ]);

        $records = $builder->paginate($params['pagination_limit']);

        foreach ($records as $record) {
            $record->afterFind();
        }

        return $records;
    }

    protected function beforeCreate() {}

    protected function beforeEdit($model)
    {
        if ($model->tableHaveField($model->getTable(), "is_pre_defined")) {
            if ($model->is_pre_defined) {
                abort(ACTION_NOT_PROCEED, "Pre-Defined Record can not be edit");
            }
        }

        if ($model->tableHaveField($model->getTable(), "can_edit")) {
            if (!$model->can_edit) {
                $msg = "Record can not be edit";
                if ($model->tableHaveField($model->getTable(), "cannot_edit_reason_array") || array_key_exists("cannot_edit_reason_array", $model->attributes)) {

                    if ($model->cannot_edit_reason_array) {
                        $arr = $model->cannot_edit_reason_array;
                        $msg .= ", Reason : " . implode(", ", $arr);
                    }
                }

                abort(ACTION_NOT_PROCEED, $msg);
            }
        }
    }

    protected function updateUserInfoAfterSave(Model $model)
    {
       
    }

    protected function updateUserInfoAfterDelete(Model $model)
    {
        
    }

    protected function updateUserInfoForCreateBulk(array $models)
    {
        
    }

    protected function updateUserInfoForUpdateBulk(array $models)
    {
        
    }

    protected function updateUserInfoForDeleteBulk(array $models)
    {
        
    }

    protected function afterSave($model, $prefix = null, $msg = null)
    {
        $method_name = $this->getMethodName();

        if (!$prefix) {
            $prefix = $model->getClassDisplayName();
        }

        if (!$msg) {
            if ($method_name == "store") {
                $msg = "$prefix Created successfully";
            } else if ($method_name == "update") {
                $msg = "$prefix Updated successfully";
            } else {
                $msg = "$prefix Saved successfully";
            }

            $msg = trim($msg);
        }

        if (!$msg) {
            throw_exception("msg variable required");
        }

        $request = request();

        if ($request->ajax()) {
            return $this->responseJson(['status' => 1, 'msg' => $msg]);
        }

        $this->saveSqlLog();

        $redirect_to_index = (bool) request("redirect_to_index", false);

        if ($redirect_to_index) {
            return redirect()->route($this->routePrefix . ".index")->with('success', $msg);
        }

        $redirect_to_add = (bool) request("redirect_to_add", false);

        if ($redirect_to_add) {
            return redirect()->route($this->routePrefix . ".create")->with('success', $msg);
        }

        $redirect_to_edit = (bool) request("redirect_to_edit", false);

        if ($redirect_to_edit) {
            return redirect()->route($this->routePrefix . ".edit", [$model])->with('success', $msg);
        }
        
        return back()->with('success', $msg);
    }

    protected function beforeDelete($model)
    {
        if ($model->tableHaveField($model->getTable(), "is_pre_defined")) {
            if ($model->is_pre_defined) {
                abort(ACTION_NOT_PROCEED, "Pre-Defined Record can not be delete");
            }
        }

        if ($model->tableHaveField($model->getTable(), "can_delete")) {
            if (!$model->can_delete) {
                $msg = "Record can not be delete";
                if ($model->tableHaveField($model->getTable(), "cannot_delete_reason_array") || array_key_exists("cannot_delete_reason_array", $model->attributes)) {
                    if ($model->cannot_delete_reason_array) {
                        $msg .= ", Reason : " . implode(", ", $model->cannot_delete_reason_array);
                    }
                }

                abort(ACTION_NOT_PROCEED, $msg);
            }
        }
    }

    protected function delete(BaseModelInterface $model)
    {
        try {
            DB::beginTransaction();

            $this->deleteCascade($model);

            DB::commit();

            return true;
        } catch (\Exception $ex) {
            DB::rollBack();

            throw $ex;
        }
    }

    protected function afterDestroy($model, $prefix = null, $msg = null)
    {
        $request = request();

        if (!$prefix) {
            $prefix = $model->getClassDisplayName();
        }

        if (!$msg) {
            $msg = "$prefix deleted successfully";

            $msg = trim($msg);
        }

        if (!$msg) {
            throw_exception("msg variable required");
        }

        if ($request->ajax()) {
            return $this->responseJson(['status' => 1, 'msg' => $msg]);
        }

        $this->saveSqlLog();

        $redirect_to = request("redirect_to", false);

        if ($redirect_to)
        {
            return redirect()->route($this->routePrefix . "." . $redirect_to)->with('success', $msg);
        }

        return back()->with('success', $msg);
    }

    protected function beforeCsvImport()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(60 * 60);
    }

    protected function beforeCsvExport(Builder $builder)
    {
        $max_count = Setting::getValueOrFail("export_csv_max_record_count");

        $record_count = $builder->count();

        if ($record_count > $max_count) {
            abort(ACTION_NOT_PROCEED, "Record Count is $record_count which is more than Max Limit $max_count");
        }

        ini_set('memory_limit', '1024M');
        set_time_limit(60 * 10);
    }

    protected function beforePdfExport(Builder $builder)
    {
        $max_count = Setting::getValueOrFail("export_pdf_max_record_count");

        $record_count = $builder->count();

        if ($record_count > $max_count) {
            abort(ACTION_NOT_PROCEED, "Record Count is $record_count which is more than Max Limit $max_count");
        }

        ini_set('memory_limit', '1024M');
        set_time_limit(60 * 10);
    }
}
