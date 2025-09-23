<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Helpers\DateUtility;
use App\Models\BaseModel;
use App\Models\Setting;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
=======
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
>>>>>>> base_v3
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WebController extends Controller
{
<<<<<<< HEAD
=======
    use CacheTrait;

>>>>>>> base_v3
    /**
     * variable require for view
     */
    protected String $routePrefix, $viewPrefix, $tableName, $layout;

    protected $modelClass;

<<<<<<< HEAD
    protected int $paginationLimit = 10;
=======
    protected int $paginationLimit = 100;
>>>>>>> base_v3

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
<<<<<<< HEAD
=======

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
>>>>>>> base_v3
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
<<<<<<< HEAD
        if (request()->ajax()) {
=======
        if (request()->ajax() && !request("full_view")) {
>>>>>>> base_v3
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

<<<<<<< HEAD
        if ($method_name == "index") {
        } else if ($method_name == "show") {
            $sub_title = "View";
        } else {
            $sub_title = str_replace("index", "Summary", $method_name);
=======
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
>>>>>>> base_v3

            $sub_title = str_function_name_to_human_text($sub_title);
        }

<<<<<<< HEAD
        $module_name = $this->getModuleName();

=======
>>>>>>> base_v3
        $page_title = trim($module_name);

        if (isset($sub_title) && $sub_title) {
            $page_title .= " | " . $sub_title;
        }

        $page_title = ucwords($page_title);

        return $page_title;
    }

    protected function getRequestData($cache_prefix, array $array, bool $is_cache_clear = false)
    {
<<<<<<< HEAD
        if ($cache_prefix) {
            $cache_key = BaseModel::optimizeCacheKey(get_cache_prefix() . "-request_" . $cache_prefix . "_" . auth()->id());

            if (Cache::has($cache_key)) {
                $cache_params = Cache::get($cache_key);
=======
        $cache_params = [];

        if (is_string($cache_prefix) && $cache_prefix) {
            $cache_key = "request_" . $cache_prefix . "_user-" . $this->getAuthUserId();

            $cache_params = self::getCache($cache_key);

            if (!$cache_params) {
                $cache_params = [];
>>>>>>> base_v3
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
<<<<<<< HEAD
                Cache::forget($cache_key);
            } else {
                if (!Cache::put($cache_key, $params, laravel_constant("cache_time.summary_search"))) {
                    throw_exception("Fail to put cache");
                }
=======
                self::forgotCache($cache_key);
            } else {
                self::addCache($cache_key, $params, laravel_constant("cache_time.summary_search"));
>>>>>>> base_v3
            }
        }

        return $params;
    }

<<<<<<< HEAD
    protected function getConditions($cache_prefix, array $array, bool $return_key_value_pair_conditions = false)
    {
        $conditions = [];
        $search_variables = [];

        if ($cache_prefix) {
            $cache_key = BaseModel::optimizeCacheKey(get_cache_prefix() . "-search_" . $cache_prefix . "_" . auth()->id());
=======
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
>>>>>>> base_v3
        }

        $request_params = request()->all();

<<<<<<< HEAD
        if (empty($request_params) && isset($cache_key)) {
            if (Cache::has($cache_key)) {
                $request_params = Cache::get($cache_key);
=======
        foreach ($request_params as $k => $v) {
            if (is_string($v)) {
                $request_params[$k] = urldecode($v);
            }
        }

        if (empty($request_params) && isset($cache_key)) {
            $cache_params = self::getCache($cache_key);

            if ($cache_params) {
                $request_params = $cache_params;
>>>>>>> base_v3
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
<<<<<<< HEAD
=======
            $extra = $row['extra'] ?? [];
>>>>>>> base_v3

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

<<<<<<< HEAD
                        $arr = $this->parseConditionValue($v, $row['type']);
=======
                        $arr = $this->parseConditionValue($v, $row['type'], $extra);
>>>>>>> base_v3
                        $value[$k] = $arr['value'];
                    }

                    $parse_value = implode(",", $value);
                    if ($return_key_value_pair_conditions) {
<<<<<<< HEAD
                        if ($op && $op != "=") {
                            $conditions[$field . " " . $op] = $parse_value;
                        } else {
                            $conditions[$field] = $parse_value;
                        }
=======
                        $conditions[$field] = [
                            "value" => $parse_value,
                            "op" => $op,
                        ];
>>>>>>> base_v3
                    } else {
                        $conditions[] = [$field, $op, $parse_value];
                    }
                }
            } else if (strlen($value) > 0) {
<<<<<<< HEAD
                $arr = $this->parseConditionValue($value, $row['type']);
=======
                $arr = $this->parseConditionValue($value, $row['type'], $extra);
>>>>>>> base_v3

                $parse_value = $arr['value'];
                $op = $arr['op'];

                if ($return_key_value_pair_conditions) {
<<<<<<< HEAD
                    if ($op && $op != "=") {
                        $conditions[$field . " " . $op] = $parse_value;
                    } else {
                        $conditions[$field] = $parse_value;
                    }
=======
                    $conditions[$field] = [
                        "value" => $parse_value,
                        "op" => $op,
                    ];
>>>>>>> base_v3
                } else {
                    $conditions[] = [$field, $op, $parse_value];
                }
            }
<<<<<<< HEAD
        }

        if (isset($cache_key)) {
            if (!Cache::put($cache_key, $search_variables, laravel_constant("cache_time.summary_search"))) {
                throw_exception("Fail to put cache");
            }
=======

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
>>>>>>> base_v3
        }

        if (!isset($this->data['search'])) {
            $this->data['search'] = [];
        }

        $this->data['search'] = array_merge($this->data['search'], $search_variables);

        return $conditions;
    }

<<<<<<< HEAD
    protected function parseConditionValue($value, String $type)
=======
    protected function parseConditionValue($value, String $type, array $extra = [])
>>>>>>> base_v3
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
<<<<<<< HEAD
                $parse_value = "%" . $value . "%";
                $op = 'LIKE';
                break;

            case "date":
                $parse_value = DateUtility::getDate($value, DateUtility::DATE_FORMAT);
                break;

            case "from_date":
                $parse_value = DateUtility::getDate($value, DateUtility::DATE_FORMAT);
=======
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
>>>>>>> base_v3
                $op = '>=';
                break;

            case "to_date":
<<<<<<< HEAD
                $parse_value = DateUtility::getDate($value, DateUtility::DATE_FORMAT);
                $op = '<=';
                break;

            case "from_datetime":
                $parse_value = DateUtility::getDate($value);
=======
                $format = DateUtility::DATE_FORMAT;
                $op = '<=';
                break;

            case "datetime":
                $format = DateUtility::DATETIME_FORMAT;
                break;

            case "from_datetime":
                $format = DateUtility::DATETIME_FORMAT;
>>>>>>> base_v3
                $op = '>=';
                break;

            case "to_datetime":
<<<<<<< HEAD
                $parse_value = DateUtility::getDate($value);
=======
                $format = DateUtility::DATETIME_FORMAT;
>>>>>>> base_v3
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

<<<<<<< HEAD
=======
        if (in_array($type, ["date", "from_date", "to_date", "datetime", "from_datetime", "to_datetime"])) {
            $parse_value = DateUtility::getDate($value, $format, DateUtility::$login_user_time_zone);
        }

>>>>>>> base_v3
        return [
            "value" => $parse_value,
            "op" => $op
        ];
    }

<<<<<<< HEAD
    protected function getPaginagteRecords(Builder $builder, $cache_key_prefix)
    {
        $cache_key = BaseModel::optimizeCacheKey( get_cache_prefix() . "-pagination_limit-" . $cache_key_prefix . "_" . auth()->id() );
=======
    protected function getPaginagteRecords($builder, String $cache_key_prefix)
    {
        $cache_key = $cache_key_prefix . "-pagination_limit";
>>>>>>> base_v3

        $params = $this->getRequestData($cache_key, [
            ["key" => "pagination_limit", "default" => $this->paginationLimit],
        ]);

        if ($params['pagination_limit'] < 1) {
            throw_exception("pagination_limit can not be less than 1");
        }

<<<<<<< HEAD
        $this->setForView([
            "pagination_limit" => $params['pagination_limit']
        ]);

        // d($params['pagination_limit']); exit;

        return $builder->paginate($params['pagination_limit']);
=======
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
>>>>>>> base_v3
    }

    protected function beforeCreate() {}

    protected function beforeEdit($model)
    {
<<<<<<< HEAD
        if (BaseModel::tableHaveField($model->getTable(), "is_pre_defined")) {
=======
        if ($model->tableHaveField($model->getTable(), "is_pre_defined")) {
>>>>>>> base_v3
            if ($model->is_pre_defined) {
                abort(ACTION_NOT_PROCEED, "Pre-Defined Record can not be edit");
            }
        }

<<<<<<< HEAD
        if (BaseModel::tableHaveField($model->getTable(), "can_edit")) {
            if (!$model->can_edit) {
                $msg = "Record can not be edit";
                if (BaseModel::tableHaveField($model->getTable(), "cannot_edit_reason_array") || array_key_exists("cannot_edit_reason_array", $model->attributes)) {
                    if ($model->cannot_edit_reason_array) {
                        $msg .= "Reason : " . implode(", ", $model->cannot_edit_reason_array);
=======
        if ($model->tableHaveField($model->getTable(), "can_edit")) {
            if (!$model->can_edit) {
                $msg = "Record can not be edit";
                if ($model->tableHaveField($model->getTable(), "cannot_edit_reason_array") || array_key_exists("cannot_edit_reason_array", $model->attributes)) {

                    if ($model->cannot_edit_reason_array) {
                        $arr = $model->cannot_edit_reason_array;
                        $msg .= ", Reason : " . implode(", ", $arr);
>>>>>>> base_v3
                    }
                }

                abort(ACTION_NOT_PROCEED, $msg);
            }
        }
    }

    protected function updateUserInfoAfterSave(Model $model)
    {
<<<<<<< HEAD
        if (App::environment('production'))
        {
            return false;
        }

        $auth_user = Auth::user();
        
        if ($auth_user)
        {
            // dd($model->wasRecentlyCreated);
            if ($model->wasRecentlyCreated) 
            {
                $auth_user->changeCreatedByUsage($model, 1);
            }
            else 
            {
                $auth_user->changeUpdatedByUsage($model, 1);
            }

            // d($this->getQueryLog());
            // exit;
        }
=======
       
>>>>>>> base_v3
    }

    protected function updateUserInfoAfterDelete(Model $model)
    {
<<<<<<< HEAD
        $auth_user = Auth::user();
        if ($auth_user) {
            die("Under Development");
        }
=======
        
>>>>>>> base_v3
    }

    protected function updateUserInfoForCreateBulk(array $models)
    {
<<<<<<< HEAD
        if (App::environment('production'))
        {
            return false;
        }

        $auth_user = Auth::user();

        if ($auth_user) {
            $auth_user->changeCreatedByUsageBulk($models);
        }
=======
        
>>>>>>> base_v3
    }

    protected function updateUserInfoForUpdateBulk(array $models)
    {
<<<<<<< HEAD
        if (App::environment('production'))
        {
            return false;
        }

        $auth_user = Auth::user();

        if ($auth_user) {
            $auth_user->changeUpdatedByUsageBulk($models);
        }
=======
        
>>>>>>> base_v3
    }

    protected function updateUserInfoForDeleteBulk(array $models)
    {
<<<<<<< HEAD
        if (App::environment('production'))
        {
            return false;
        }
        
        $auth_user = Auth::user();
        // $auth_user = (User) $auth_user;

        if ($auth_user) {
            die("Under Development");
        }
=======
        
>>>>>>> base_v3
    }

    protected function afterSave($model, $prefix = null, $msg = null)
    {
        $method_name = $this->getMethodName();

<<<<<<< HEAD
        if (is_null($prefix)) {
            $prefix = str_class_name_to_human_text(get_class($model));
=======
        if (!$prefix) {
            $prefix = $model->getClassDisplayName();
>>>>>>> base_v3
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

<<<<<<< HEAD
=======
        $request = request();

        if ($request->ajax()) {
            return $this->responseJson(['status' => 1, 'msg' => $msg]);
        }

        $this->saveSqlLog();

>>>>>>> base_v3
        $redirect_to_index = (bool) request("redirect_to_index", false);

        if ($redirect_to_index) {
            return redirect()->route($this->routePrefix . ".index")->with('success', $msg);
        }

<<<<<<< HEAD
=======
        $redirect_to_add = (bool) request("redirect_to_add", false);

        if ($redirect_to_add) {
            return redirect()->route($this->routePrefix . ".create")->with('success', $msg);
        }

        $redirect_to_edit = (bool) request("redirect_to_edit", false);

        if ($redirect_to_edit) {
            return redirect()->route($this->routePrefix . ".edit", [$model])->with('success', $msg);
        }
        
>>>>>>> base_v3
        return back()->with('success', $msg);
    }

    protected function beforeDelete($model)
    {
<<<<<<< HEAD
        if (BaseModel::tableHaveField($model->getTable(), "is_pre_defined")) {
=======
        if ($model->tableHaveField($model->getTable(), "is_pre_defined")) {
>>>>>>> base_v3
            if ($model->is_pre_defined) {
                abort(ACTION_NOT_PROCEED, "Pre-Defined Record can not be delete");
            }
        }

<<<<<<< HEAD
        if (BaseModel::tableHaveField($model->getTable(), "can_delete")) {
            if (!$model->can_delete) {
                $msg = "Record can not be delete";
                if (BaseModel::tableHaveField($model->getTable(), "cannot_delete_reason_array") || array_key_exists("cannot_delete_reason_array", $model->attributes)) {
                    if ($model->cannot_delete_reason_array) {
                        $msg .= "Reason : " . implode(", ", $model->cannot_delete_reason_array);
=======
        if ($model->tableHaveField($model->getTable(), "can_delete")) {
            if (!$model->can_delete) {
                $msg = "Record can not be delete";
                if ($model->tableHaveField($model->getTable(), "cannot_delete_reason_array") || array_key_exists("cannot_delete_reason_array", $model->attributes)) {
                    if ($model->cannot_delete_reason_array) {
                        $msg .= ", Reason : " . implode(", ", $model->cannot_delete_reason_array);
>>>>>>> base_v3
                    }
                }

                abort(ACTION_NOT_PROCEED, $msg);
            }
        }
    }

<<<<<<< HEAD
    protected function delete(BaseModel $model)
=======
    protected function delete(BaseModelInterface $model)
>>>>>>> base_v3
    {
        try {
            DB::beginTransaction();

<<<<<<< HEAD
            $this->beforeDelete($model);

=======
>>>>>>> base_v3
            $this->deleteCascade($model);

            DB::commit();

            return true;
        } catch (\Exception $ex) {
            DB::rollBack();

            throw $ex;
        }
    }

<<<<<<< HEAD
    protected function afterDestroy(Model $model, $prefix = null, $msg = null)
    {
        $request = request();

        if (is_null($prefix)) {
            $prefix = str_class_name_without_namespace(get_class($model));
=======
    protected function afterDestroy($model, $prefix = null, $msg = null)
    {
        $request = request();

        if (!$prefix) {
            $prefix = $model->getClassDisplayName();
>>>>>>> base_v3
        }

        if (!$msg) {
            $msg = "$prefix deleted successfully";

            $msg = trim($msg);
        }

        if (!$msg) {
            throw_exception("msg variable required");
        }

        if ($request->ajax()) {
<<<<<<< HEAD
            return response()->json(['status' => 1, 'msg' => $msg]);
        }

        $redirect_to = request("redirect_to", "index");

        return redirect()->route($this->routePrefix . "." . $redirect_to)->with('success', $msg);
=======
            return $this->responseJson(['status' => 1, 'msg' => $msg]);
        }

        $this->saveSqlLog();

        $redirect_to = request("redirect_to", false);

        if ($redirect_to)
        {
            return redirect()->route($this->routePrefix . "." . $redirect_to)->with('success', $msg);
        }

        return back()->with('success', $msg);
>>>>>>> base_v3
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
