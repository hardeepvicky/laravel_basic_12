<?php

namespace App\Helpers;

use App\Exceptions\UserException;
use App\Jobs\Backend\JobForgotPassword;
use App\Jobs\Backend\JobSendEmailUserOtp;
use App\Jobs\Backend\JobSendEmailOnRegisration;
use App\Jobs\Backend\JobSendWhatappUserOtp;
use App\Models\BaseModelInterface;
use App\Models\Role;
use App\Models\SqlLog;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use PDOException;
use ReflectionClass;
use Illuminate\Support\Str;

class LaravelExtend
{
    public static array $warnings = [], $errors = [], $info = [];

    public static function getRoutes()
    {
        $routeCollections = \Illuminate\Support\Facades\Route::getRoutes();

        $routes = [];
        foreach ($routeCollections as $routeObj) {

            $route = [
                'url' => $routeObj->uri,
                'route_name' => $routeObj->getName(),
                "action" => $routeObj->getActionName(),
            ];

            if (strpos($route['route_name'], "debugbar.") !== false) {
                continue;
            }

            if (strpos($route['route_name'], "sanctum.") !== false) {
                continue;
            }

            if ($route['route_name']) {
                $routes[] = $route;
            }
        }

        //d($routes); exit;

        return $routes;
    }

    public static function send_email_user_otp(User $user)
    {
        $user->otp = Util::getRandomString(6, "1234567890");
        $user->otp_sent_datetime = date("Y-m-d H:i:s");

        if (!$user->save()) {
            throw_exception("User Save Failed");
        }

        if ($user->dont_send_email)
        {
            return;
        }

        $link = url()->query(config("url") . "/otp_verify/" . $user->uid, ["otp_sent" => true]);

        $job = new JobSendEmailUserOtp($user, $link);

        dispatch($job);
    }

    public static function send_whatsapp_user_otp(User $user)
    {
        if (!$user->whatsapp_mobile)
        {
            throw new UserException("Whatsapp Mobile No. not found in database");
        }

        $user->otp = Util::getRandomString(6, "1234567890");
        $user->otp_sent_datetime = date("Y-m-d H:i:s");

        if (!$user->save()) {
            throw_exception("User Save Failed");
        }

        $user->load("country");

        $job = new JobSendWhatappUserOtp($user->full_name, $user->mobile, $user->country->phonecode, $user->otp, $user->id);

        dispatch($job);
    }

    public static function send_email_on_registeration(User $user)
    {
        $job = new JobSendEmailOnRegisration($user);

        dispatch($job);
    }

    public static function send_forgot_password_link(User $user)
    {
        if (!$user->uid) {
            throw new Exception("UId is not generated");
        }

        $link =  config("url") . "/create_password/" . $user->uid;

        $job = new JobForgotPassword($user, $link);

        dispatch($job);
    }

    public static function check_database_connection($connectionName = 'mysql', bool $should_throw_error = false)
    {
        try {
            // Attempt to get the PDO instance for the specified connection
            DB::connection($connectionName)->getPdo();

            // If no exception is thrown, the connection is successful
            $databaseName = DB::connection($connectionName)->getDatabaseName();

            if ($databaseName) {
                return $databaseName;
            }
        } catch (PDOException $e) {
            if ($should_throw_error) {
                throw $e;
            }
            // If a PDOException occurs, the connection failed
        } catch (\Exception $e) {
            if ($should_throw_error) {
                throw $e;
            }
        }

        return false;
    }

    public static function isRequestFromLocal()
    {
        $host = Request::host();

        $request = request();

        if (in_array($host, ["localhost", "dev.erp.com", "dev.celecerp.com"])) {
            return true;
        }

        $arr = explode(".", $host);

        if (count($arr) == 4) {
            return true;
        }

        return false;
    }

    public static function merge_validation_rules($old_rules, $new_rules)
    {
        foreach ($new_rules as $k => $rules) {
            if (is_array($rules)) {
                if (isset($old_rules[$k])) {
                    $old_rules[$k] = array_merge($old_rules[$k], $rules);
                } else {
                    $old_rules[$k] = $rules;
                }
            }
        }

        return $old_rules;
    }

    public static function getQueryLog($connection = "", int $length = 0)
    {
        if ($connection) {
            $db_logs = DB::connection($connection)->getQueryLog();
        } else {
            $db_logs = DB::getQueryLog();
        }

        foreach ($db_logs as $k => $row) {
            $query = $row['query'];

            $query = vsprintf(str_replace('?', "'%s'", $query), $row['bindings']);

            $row['query'] = trim(preg_replace('/\s+/', ' ', $query));

            unset($row['bindings']);

            $db_logs[$k] = $row;
        }

        $total_count = count($db_logs);

        if ($total_count > 0)
        {
            if ($length != 0)
            {
                if ($length < 0)
                {
                    $temp = $db_logs;
                    unset($db_logs);

                    $length = abs($length);
                    $start = $total_count - $length;

                    for($i = $start; $i < $total_count; $i++)
                    {
                        $db_logs[] = $temp[$i];
                    }
                }
                else if ($length > 0)
                {
                    $temp = $db_logs;
                    unset($db_logs);

                    for($i = 0; $i < $length; $i++)
                    {
                        $db_logs[] = $temp[$i];
                    }
                }
            }
        }

        return $db_logs;
    }

    public static function getMongodbQueryLog($connection = "mongodb")
    {
        $db_logs = DB::connection($connection)->getQueryLog();

        foreach ($db_logs as $k => $row) {
            $query = $row['query'];

            $row['query'] = trim(preg_replace('/\s+/', ' ', $query));

            unset($row['bindings']);

            $db_logs[$k] = $row;
        }

        return $db_logs;
    }

    public static function getCreatedByOrUpdatedBy($id, $userList = [])
    {
        if ($id === 0) {
            return "system";
        }

        if (empty($userList)) {
            $userList = User::getListCache();
        }

        return $userList[$id] ?? '';
    }

    public static function getListOfModels()
    {
        $models = [];
        $path = app_path('Models');

        foreach (scandir($path) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            if (str_ends_with($file, '.php')) {
                $model_name = pathinfo($file, PATHINFO_FILENAME);

                if (in_array($model_name, ["BaseModel", "BaseModelInterface", "BaseMongoModel"]))
                {
                    continue;
                }

                $models[$model_name] = 'App\\Models\\' . $model_name;
            }
        }

        return $models;
    }

    /**
     * Checks if a model has a specific Eloquent relationship defined.
     *
     * @param Model|string $modelInstanceOrClass The model instance or fully qualified class name.
     * @param string $relationName The name of the relation method (e.g., 'posts', 'user').
     */
    public static function getRelation(Model|string $modelInstanceOrClass, string $relationName) : Relation | null
    {
        try {
            $model = is_string($modelInstanceOrClass) ? app($modelInstanceOrClass) : $modelInstanceOrClass;
            $reflectionClass = new ReflectionClass($model);

            // Check if the method exists
            if (!$reflectionClass->hasMethod($relationName)) {
                return null;
            }

            $method = $reflectionClass->getMethod($relationName);

            // Check if the method is public and has no required parameters
            // (Eloquent relations are typically public methods with no mandatory params)
            if (!$method->isPublic() || $method->getNumberOfRequiredParameters() > 0) {
                return null;
            }

            // Attempt to invoke the method on a new instance of the model
            // We use a new instance to avoid side effects on an existing one
            // and to ensure all dynamic properties are set up.
            $relation = $method->invoke($model);

            // Check if the returned value is an instance of a Laravel Relation
            if ( $relation instanceof Relation)
            {
                return $relation;
            }

        } catch (\ReflectionException $e) {
            // Method or class not found, or other reflection error
            return null;
        } catch (\Throwable $e) {
            // Any other exception during invocation (e.g., if relation method relies on instantiated data)
            // This is a safety net; ideally, relation methods don't throw on clean instances.
            return null;
        }
    }

    public static function getViewLinkForModel(String $model_name, $id)
    {
        $placeholder = Str::snake($model_name);

        $route_prefix = self::getRoutePrefixForModel($model_name);

        $route_postfix_array = ["index"];

        foreach($route_postfix_array as $route_postfix)
        {
            if (Route::has($route_prefix . "." . $route_postfix))
            {
                $params = [];

                if ($route_postfix == "show")
                {
                    $params = [$placeholder => $id];
                }
                else if ($route_postfix == "index")
                {
                    $params = ["id" => $id];
                }

                return route($route_prefix . "." . $route_postfix, $params);
            }
        }

        return null;
    }

    public static function getRoutePrefixForModel(String $model_name)
    {
        $str = trim($model_name);

        $str = str_space_after_every_captial_letter($str);

        $str = str_replace(" ", "-", $str);

        $str = strtolower($str);

        $str = trim($str, "-");

        return $str;
    }

    public static function checkDatabaseExist($connection = "mysql")
    {
        $databaseName = config('database.connections.' . $connection . '.database'); // Get configured database name

        try {
            $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?";
            $dbExists = DB::select($query, [$databaseName]);

            if (empty($dbExists)) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function addWarning($label, $messages, $group = null, $extra = [])
    {
        if (!is_array($messages)) {
            $messages = [$messages];
        }

        if (!$group) {
            $group = 0;
        }

        if (!is_scalar($group)) {
            throw new UserException("group should be scaler value");
        }

        foreach ($messages as $msg) {
            self::$warnings[$group][] = compact("label", "msg", "extra");
        }
    }

    public static function addError($label, $messages, $group = null, $extra = [])
    {
        if (!is_array($messages)) {
            $messages = [$messages];
        }

        if (!$group) {
            $group = 0;
        }

        if (!is_scalar($group)) {
            throw new UserException("group should be scaler value");
        }

        foreach ($messages as $msg) {
            self::$errors[$group][] = compact("label", "msg", "extra");
        }
    }

    public static function addInfo($messages, string $group, $extra = [])
    {
        if (!is_array($messages)) {
            $messages = [$messages];
        }

        foreach ($messages as $msg) {
            self::$info[$group][] = compact("msg", "extra");
        }
    }

    public static function getAuthUserId()
    {
        $auth_user = Auth::user();

        if ($auth_user)
        {
            return $auth_user->id;
        }

        return 0;
    }

    public static function getAdminUserList()
    {
        $user_list = User::whereHas("roles", function($q){
            $q->where("code", Role::TYPE_SYSTEM_ADMIN);
        })->pluck("name", "id")->toArray();

        return $user_list;
    }

    public static function saveSqlLog(array $save_data = []) : SqlLog
    {
        $sqlLogModel = new SqlLog();

        $sqlLogModel->fill($save_data);

        $logs = SqlLog::fetchSqlLog();

        if (count($logs) == 0) {
            return $sqlLogModel;
        }

        // d($logs, true);

        $sqlLogModel->auth_user_id = LaravelExtend::getAuthUserId();
        $sqlLogModel->have_dml_query = 0;
        $sqlLogModel->have_heavy_query = 0;
        $sqlLogModel->sql_query_count = 0;
        $sqlLogModel->dml_query_count = 0;
        $sqlLogModel->heavy_query_count = 0;
        $sqlLogModel->files = [];

        if (!$sqlLogModel->saveQuietly()) {
            throw new UserException("Fail to Save Sql Log");
        }

        $base_file_name = date_get_for_file_name() . "_";

        $disk = SqlLog::getStorageDisk();

        $fileUtility = new FileUtility($disk);

        $path = SqlLog::getStoragePath();

        $files = [];

        foreach($logs as $connectionName => $db_logs)
        {
            if (isset($db_logs['sql_list']) && $db_logs['sql_list'])
            {
                $sqlLogModel->sql_query_count += count($db_logs['sql_list']);

                $filename = $connectionName . "_" . $base_file_name . ".sql";
                $content = implode(";" . PHP_EOL, $db_logs['sql_list']);

                $fileUtility->writeContent($path, $filename, $content);

                $files[] = [
                    "larvel_connection_key" => $connectionName,
                    "label" => "SQL",
                    "file" => $path . $filename
                ];
            }

            if (isset($db_logs['select_sql_list']) && $db_logs['select_sql_list'])
            {
                $list = [];
                $keys = array_keys($db_logs['select_sql_list'][0]);

                $list[] = implode(",", $keys);

                foreach($db_logs['select_sql_list'] as $arr)
                {
                    $list[] = implode(",", $arr);
                }

                $filename = $connectionName . "_" . $base_file_name . "select.txt";
                $content = implode(PHP_EOL, $list);

                $fileUtility->writeContent($path, $filename, $content);

                $files[] = [
                    "larvel_connection_key" => $connectionName,
                    "label" => "Select SQL",
                    "file" => $path . $filename
                ];
            }

            if (isset($db_logs['dml_sql_list']) && $db_logs['dml_sql_list'])
            {
                $sqlLogModel->dml_query_count += count($db_logs['dml_sql_list']);

                $filename = $connectionName . "_" . $base_file_name . "dml.sql";

                $content = implode(";" . PHP_EOL, $db_logs['dml_sql_list']);

                $fileUtility->writeContent($path, $filename, $content);

                $files[] = [
                    "larvel_connection_key" => $connectionName,
                    "label" => "DML SQL",
                    "file" => $path . $filename
                ];

                if ($db_logs['have_dml_query'])
                {
                    $sqlLogModel->have_dml_query = 1;
                }
            }

            if (isset($db_logs['heavy_sql_list']) && $db_logs['heavy_sql_list']) {

                $sqlLogModel->heavy_query_count += count($db_logs['heavy_sql_list']);

                $list = [];

                $keys = array_keys($db_logs['heavy_sql_list'][0]);

                $list[] = implode(",", $keys);

                foreach($db_logs['heavy_sql_list'] as $arr)
                {
                    $list[] = implode(",", $arr);
                }

                $filename = $connectionName . "_" . $base_file_name . "heavy.txt";

                $content = implode(PHP_EOL, $list);

                $fileUtility->writeContent($path, $filename, $content);

                $files[] = [
                    "larvel_connection_key" => $connectionName,
                    "label" => "Heavy SQL",
                    "file" => $path . $filename
                ];

                if ($db_logs['have_heavy_query'])
                {
                    $sqlLogModel->have_heavy_query = 1;
                }
            }
        }

        $sqlLogModel->files = $files;

        if (!$sqlLogModel->saveQuietly()) {
            throw new Exception("Fail to Save Sql Log");
        }

        return $sqlLogModel;
    }

    public static function getMongodbParseCondition($value, String $type, array $extra = [])
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

            case "elemmatch":
                $op = "elemMatch";

                switch($extra['elemMatch_type'])
                {
                    case "string":
                        $sub_value = new \MongoDB\BSON\Regex($value, 'i');
                        break;

                    default:
                        $sub_value = self::getParseValueForMongoDB($value, $extra['elemMatch_type']);
                    break;
                }
                
                $parse_value = [
                    $extra['elemMatch_field'] => $sub_value
                ];
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

    public static function getParseValueForMongoDB($value, String $type)
    {
        switch($value)
        {
            case "date":
                $parse_value = Carbon::parse($value);
                break;

            case "to_datetime":
                $parse_value = Carbon::parse($value);
                break;

            case "int":
                $parse_value = (int) $value;
                break;

            case "float":
                $parse_value = (float) $value;
                break;                

            case "bool":
            case "boolean":
                $parse_value = (bool) $value;
                break;

            default:
                $parse_value = $value;
        }

        return $parse_value;
    }

    public static function writeQueryLog(array $db_logs)
    {
        $auth_user = Auth::user();

        $history = [];

        Log::info("-------------------------------QUERY LOG-------------------------------");

        if ($auth_user)
        {
            Log::info("--- Created By : " . $auth_user->getDisplayName() . " ---");
        }

        foreach(debug_backtrace() as $i => $callBy)
        {
            if ($i > 0 && $i < 6)
            {
                $arr = [
                    "File" => $callBy['file'],
                    "Line No." => $callBy['line'],
                ];

                if (isset($callBy['class'])) 
                {
                    $arr['class'] = $callBy['class'];
                }

                if (isset($callBy['function'])) {
                    $arr['function'] = $callBy['function'];
                }

                $history[] = $arr;
            }
        }

        foreach($db_logs as $db_log)
        {
            Log::debug("Query :" . $db_log['query']);

            unset($db_log['query']);

            Log::info("Query Info", $db_log);
        }

        Log::debug($history);

        Log::info("-------------------------------END QUERY LOG-------------------------------");
    }

    public static function getCacheKeyFromConditions(array $conditions = [], string $prefix = "") : string
    {
        $list = [];

        foreach($conditions as $k => $condition)
        {
            if (is_numeric($k))
            {
                $list[] = Str::snake(implode("_", $condition));
            }
            else
            {
                if (is_array($condition))
                {
                    $condition = implode(",", array_get_scaler_value_only($condition));

                    if (strlen($condition) > 50)
                    {
                        $condition = substr($condition, 0, 50);
                    }
                }

                $list[] = Str::snake($k) . "_" . Str::snake($condition);
            }
        }

        if (empty($list))
        {
            return "";
        }
        
        $cache_key = $prefix . "-" . implode("-", $list);
        $cache_key = trim(trim($cache_key, "-"));

        if (strlen($cache_key) > 255)
        {
            $cache_key = substr($cache_key, 0, 255);
        }

        return $cache_key;
    }
}
