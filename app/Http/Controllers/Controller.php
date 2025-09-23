<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Helpers\FileUtility;
use App\Models\SqlLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
=======
use App\Exceptions\UserException;
use App\Helpers\LaravelExtend;
use App\Models\AjaxLog;
use App\Models\BaseModelInterface;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
>>>>>>> base_v3
use Illuminate\Support\Facades\Route;

abstract class Controller
{
<<<<<<< HEAD
    protected function deleteCascade(Model $model)
=======
    public array $custom_errors = [];

    protected function deleteCascade(BaseModelInterface $model)
>>>>>>> base_v3
    {
        $class_name = get_class($model);

        $class_display_name = "";
<<<<<<< HEAD
        if (method_exists($model, 'getClassDisplayName'))
        {
            $class_display_name = $model->getClassDisplayName();
        }
        else
        {
            throw_exception("Please Declare getClassDisplayName() method in $class_name");
        }

        if ( !isset($model->child_model_class))
        {
            throw_exception("child_model_class Array is not set in $class_name");
        }

        foreach($model->child_model_class as $childClass => $arr)
        {
            $child_class_display_name = null;

            if (get_parent_class($childClass) == "BaseModel")
            {
                $child_class_display_name = $childClass::classDisplayName();
            }
 
            if (isset($arr['label']) && !empty($arr['label']) && is_string($arr['label']))
            {
                $child_class_display_name = $arr['label'];
            }

            if (!$child_class_display_name)
            {
                throw_exception("Please Add label in child_model_class of $class_name");
            }
            
            array_check_key_and_throw_error($arr, ["preventDelete", "foreignKey"], "$class_name - child_model_class : {key} not found");
            array_check_value_and_throw_error($arr, ["foreignKey"], "$class_name - child_model_class : {key} is empty");

            if (is_array($arr['foreignKey']))
            {
                $foreign_fields = $arr['foreignKey'];
            }
            else
            {
                $foreign_fields = [$arr['foreignKey']];
            }

            if ($arr['preventDelete'])
            {
                foreach($foreign_fields as $foreign_field)
                {
                    $count = $childClass::where($foreign_field, $model->id)->count();

                    if ($count > 0)
                    {
                        throw new \Exception("Record has associated data in $child_class_display_name. can't delete");
                    }
                }
            }
            else
            {
                foreach($foreign_fields as $foreign_field)
                {
                    $child_records = $childClass::where($foreign_field, $model->id)->get();

                    if ($child_records->count() > 0)
                    {
                        foreach($child_records as $child_record)
                        {
=======
        if (method_exists($model, 'getClassDisplayName')) {
            $class_display_name = $model->getClassDisplayName();
        } else {
            throw new UserException("Please Declare getClassDisplayName() method in $class_name");
        }

        if (!property_exists($class_name, "child_model_class")) {
            throw new UserException("child_model_class Array is not set in $class_name");
        }

        foreach ($class_name::$child_model_class as $childClass => $arr) {
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
>>>>>>> base_v3
                            $this->deleteCascade($child_record);
                        }
                    }
                }
            }
        }

<<<<<<< HEAD
        if ( !$model->delete() )
        {
            throw_exception("Fail to delete record of $class_display_name");
        }
    }
    

    protected function saveSqlLog()
    {
        if (App::environment('production'))
        {
            return false;
        }

        $db_logs = DB::getQueryLog();

        if (count($db_logs) == 0)
        {
            return false;
        }

        $sqlLogModel = new SqlLog();

        $sqlLogModel->route_name_or_url = Route::getCurrentRoute()->getName();

        if (!$sqlLogModel->route_name_or_url)
        {
            $sqlLogModel->route_name_or_url = request()->getRequestUri();
        }

        $sqlLogModel->have_dml_query = false;
        $sqlLogModel->have_heavy_query = false;

        if ( !$sqlLogModel->saveQuietly() )
        {
            throw_exception("Fail to Save Sql Log");
        }

        $sql_list = $dml_sql_list = [
            implode(",", ["Query", "Time-In-MilliSeconds"])
        ];

        foreach ($db_logs as $row)
        {
            $is_dml = false;

            $query = $row['query'];

            $query = vsprintf(str_replace('?', "'%s'", $query), $row['bindings']);

            $query = trim(preg_replace('/\s+/', ' ', $query));

            $sql_row = implode(", ", [$query, $row["time"]]);

            $sql_list[] = $sql_row;

            $query_first_10_chars = substr($query, 0, 10);
            if (!$is_dml)
            {
                $is_dml = strpos($query_first_10_chars, "INSERT") !== false;
            }

            if (!$is_dml)
            {
                $is_dml = strpos($query_first_10_chars, "insert") !== false;
            }

            if (!$is_dml)
            {
                $is_dml = strpos($query_first_10_chars, "UPDATE") !== false;
            }

            if (!$is_dml)
            {
                $is_dml = strpos($query_first_10_chars, "update") !== false;
            }

            if (!$is_dml)
            {
                $is_dml = strpos($query_first_10_chars, "DELETE") !== false;
            }

            if (!$is_dml)
            {
                $is_dml = strpos($query_first_10_chars, "delete") !== false;
            }

            if ($is_dml)
            {
                $sqlLogModel->have_dml_query = true;

                $dml_sql_list[] = $sql_row;
            }

            if ($row["time"] > 1000)
            {
                $sqlLogModel->have_heavy_query = true;
            }
        }

        $path = SqlLog::getFileSavePath() . $sqlLogModel->id . "/";
        FileUtility::createFolder($path);

        $sqlLogModel->sql_log_file = $path . "sql.txt";

        $content = implode(PHP_EOL, $sql_list);
        file_put_contents($sqlLogModel->sql_log_file, $content);

        if ($sqlLogModel->have_dml_query)
        {
            $sqlLogModel->sql_dml_log_file = $path . "sql_dml.txt";
            $content = implode(PHP_EOL, $dml_sql_list);
            file_put_contents($sqlLogModel->sql_dml_log_file, $content);
        }

        if ( !$sqlLogModel->saveQuietly() )
        {
            throw_exception("Fail to Save Sql Log");
        }

        return true;
    }

    protected function getQueryLog()
    {
        $db_logs = DB::getQueryLog();

        foreach ($db_logs as $k => $row)
        {
            $query = $row['query'];

            $query = vsprintf(str_replace('?', "'%s'", $query), $row['bindings']);

            $row['query'] = trim(preg_replace('/\s+/', ' ', $query));

            unset($row['bindings']);

            $db_logs[$k] = $row;
        }

        return $db_logs;
    }

    protected function responseJson(Array $response)
    {
        $this->saveSqlLog();

        return response()->json($response);
    }
=======
        if (!$model->delete()) {
            throw_exception("Fail to delete record of $class_display_name");
        }
    }

    protected function saveSqlLog()
    {
        if (App::environment('production')) {
            return false;
        }

        $save['route_name'] = Route::getCurrentRoute()->getName();
        $save['request_method'] = Request::method();
        $save['url'] = request()->getRequestUri();

        $model = LaravelExtend::saveSqlLog($save);

        return $model;
    }

    protected function getMongodbQueryLog()
    {
        return LaravelExtend::getMongodbQueryLog();
    }

    protected function responseJson(array $response)
    {
        if (App::environment('production')) {
            return response()->json($response);
        }

        $sql_model = $this->saveSqlLog();

        if (is_ajax_request()) {
            $request = request();

            $request_params = [];

            foreach($request->all() as $k => $v)
            {
                if (in_array($k, ["_"]))
                {

                }
                else if (is_string($v))
                {
                    $request_params[$k] = urldecode($v);
                }
            }

            $method = Request::method();

            $save = [
                "route_name" => Route::currentRouteName(),
                "url" => $_SERVER['REQUEST_URI'],
                "request_method" => $method,
                "request" => $request_params,
                "response" => $response,
                "sql_log_id" => $sql_model?->id,
            ];

            $save = array_purge_if_exceed_limit($save);
            $save = array_short_string_with_dot($save, 2048);

            $save["exec_time"] = microtime(true) - LARAVEL_START;

            $ajaxLog = new AjaxLog($save);            

            if (!$ajaxLog->saveQuietly()) {
                throw new UserException("Fail To Save Ajax Log", UserException::ERROR_LARAVEL_FAIL);
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
>>>>>>> base_v3
}
