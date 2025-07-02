<?php

namespace App\Http\Controllers;

use App\Acl\AccessControl;
use App\Exceptions\UserException;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Models\AjaxLog;
use App\Models\BaseModel;
use App\Models\BaseMongoModel;
use App\Models\CacheLog;
use App\Models\SqlLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\MessageBag;

abstract class Controller
{
    public array $custom_errors = [];

    protected function deleteCascade(Model $model)
    {
        $class_name = get_class($model);

        $class_display_name = "";
        if (method_exists($model, 'getClassDisplayName')) {
            $class_display_name = $model->getClassDisplayName();
        } else {
            throw_exception("Please Declare getClassDisplayName() method in $class_name");
        }

        if (!isset($model->child_model_class)) {
            throw_exception("child_model_class Array is not set in $class_name");
        }

        foreach ($model->child_model_class as $childClass => $arr) {
            $child_class_display_name = $childClass::classDisplayName();

            if (!$child_class_display_name) {
                if (isset($arr['label']) && !empty($arr['label']) && is_string($arr['label'])) {
                    $child_class_display_name = $arr['label'];
                }
            }

            if (!$child_class_display_name) {
                throw_exception("Please Add label in child_model_class of $class_name");
            }

            array_check_key_and_throw_error($arr, ["preventDelete", "foreignKey"], "$class_name - child_model_class : {key} not found");
            array_check_value_and_throw_error($arr, ["foreignKey"], "$class_name - child_model_class : {key} is empty");

            if (is_array($arr['foreignKey'])) {
                $foreign_fields = $arr['foreignKey'];
            } else {
                $foreign_fields = [$arr['foreignKey']];
            }

            if ($arr['preventDelete']) {
                foreach ($foreign_fields as $foreign_field) {
                    $count = $childClass::where($foreign_field, $model->id)->count();

                    if ($count > 0) {
                        throw new \Exception("Record has associated data in $child_class_display_name. can't delete");
                    }
                }
            } else {
                foreach ($foreign_fields as $foreign_field) {
                    $child_records = $childClass::where($foreign_field, $model->id)->get();

                    if ($child_records->count() > 0) {
                        foreach ($child_records as $child_record) {
                            $this->deleteCascade($child_record);
                        }
                    }
                }
            }
        }

        if (!$model->delete()) {
            throw_exception("Fail to delete record of $class_display_name");
        }
    }


    protected function saveSqlLog()
    {
        if (App::environment('production')) {
            return false;
        }

        $db_logs = $this->getQueryLog();

        if (count($db_logs) == 0) {
            return false;
        }

        // d($db_logs, true);

        $sqlLogModel = new SqlLog();

        $sqlLogModel->route_name_or_url = Route::getCurrentRoute()->getName();
        $method = Request::method();
        $sqlLogModel->request_method = $method;

        if (!$sqlLogModel->route_name_or_url) {
            $sqlLogModel->route_name_or_url = request()->getRequestUri();
        }

        $sqlLogModel->have_dml_query = $db_logs['have_dml_query'];
        $sqlLogModel->have_heavy_query = $db_logs['have_heavy_query'];

        if (!$sqlLogModel->saveQuietly()) {
            throw_exception("Fail to Save Sql Log");
        }

        $base_file_name = date_get_for_file_name() . "_";

        $disk = SqlLog::getStorageDisk();

        $fileUtility = new FileUtility($disk);

        $path = SqlLog::getStoragePath();
        $filename = $base_file_name . "_sql.txt";
        $content = implode(PHP_EOL, $db_logs['sql_list']);

        $fileUtility->writeLog($path, $filename, $content);

        $sqlLogModel->sql_log_file = $path . $filename;

        if ($sqlLogModel->have_dml_query) {
            $filename = $base_file_name . "_sql_dml.txt";

            $content = implode(PHP_EOL, $db_logs['dml_sql_list']);

            $fileUtility->writeLog($path, $filename, $content);

            $sqlLogModel->sql_dml_log_file = $path . $filename;
        }

        $cache_model = $this->saveCacheLog();

        if ($cache_model)
        {
            $cache_model->sql_log_id = $sqlLogModel->id;
            $sqlLogModel->cache_model = $cache_model->toArray();
        }

        if (!$sqlLogModel->saveQuietly()) {
            throw_exception("Fail to Save Sql Log");
        }

        return $sqlLogModel;
    }

    protected function getQueryLog()
    {   
        $db_logs = LaravelExtend::getQueryLog();

        $sql_list = $dml_sql_list = [
            implode(",", ["Query", "Time-In-MilliSeconds"])
        ];

        $ret_rows = [
            "have_dml_query" => false,
            "have_heavy_query" => false,
        ];

        foreach ($db_logs as $row) {
            $query = $row['query'];

            $sql_row = implode(", ", [$query, $row["time"]]);

            $sql_list[] = $sql_row;

            $query_first_10_chars = strtolower(substr($query, 0, 10));;

            $is_dml = str_check_char_array_exist($query_first_10_chars, ["insert", "update", "delete"]);

            if ($is_dml) {
                $dml_sql_list[] = $sql_row;
                $ret_rows['have_dml_query'] = true;
            }

            if ($row["time"] > 1000) {
                $ret_rows['have_heavy_query'] = true;
            }
        }

        $ret_rows['sql_list'] = $sql_list;
        $ret_rows['dml_sql_list'] = $dml_sql_list;

        return $ret_rows;
    }

    protected function getMongodbQueryLog()
    {
        return LaravelExtend::getMongodbQueryLog();
    }

    public function saveCacheLog()
    {
        $cache_log_level = env("APP_CACHE_LOG_LEVEL", 0);

        if (!$cache_log_level)
        {
            return null;
        }

        $save['cache_logs']["BaseModel"] = BaseModel::$cache_logs;
        $save['cache_logs']["BaseMongoModel"] = BaseMongoModel::$cache_logs;
        $save['cache_logs']["AccessControl"] = AccessControl::$cache_logs;

        $model = new CacheLog($save);

        return $model;
    }

    protected function responseJson(array $response)
    {
        if (App::environment('production')) {
            return response()->json($response);
        }

        $sql_model = $this->saveSqlLog();

         if ($sql_model) {
            $sql_model->afterFind();
        }

        $cache_model = $this->saveCacheLog();

        if (is_ajax_request()) {
            $request = request();

            $method = Request::method();

            $save = [
                "route_name_or_url" => Route::currentRouteName(),
                "url" => $_SERVER['REQUEST_URI'],
                "request_method" => $method,
                "request" => $request->all(),
                "response" => $response,
                "sql_model" => $sql_model?->toArray(),
                "cache_model" => $cache_model?->toArray()
            ];

            $save = array_purge_if_exceed_limit($save);
            $save = array_short_string_with_dot($save, 2048);

            $save["exec_time"] = microtime(true) - LARAVEL_START;

            $ajaxLog = new AjaxLog($save);

            if (!$ajaxLog->saveQuietly()) {
                throw new UserException("Fail To Save Ajax Log", UserException::ERROR_LARAVEL_FAIL);
            }

            if ($cache_model)
            {
                $cache_model->ajax_log_id = $ajaxLog->id;
                $cache_model->save();
            }
        }

        return response()->json($response);
    }

    protected function getAuthUserId(): string
    {
        $auth_user = Auth::user();
        if ($auth_user) {
            return (string) $auth_user->id;
        }

        return "";
    }

    public function addError($label, $messages, $group = null, $extra = [])
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
            $this->custom_errors[$group][] = compact("label", "msg", "extra");
        }
    }
}
