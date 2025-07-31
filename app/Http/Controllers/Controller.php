<?php

namespace App\Http\Controllers;

use App\Acl\AccessControl;
use App\Exceptions\UserException;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Models\AjaxLog;
use App\Models\BaseModel;
use App\Models\BaseModelInterface;
use App\Models\BaseMongoModel;
use App\Models\CacheLog;
use App\Models\SqlLog;
use App\Models\ViewContent;
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

    protected function deleteCascade(BaseModelInterface $model)
    {
        $class_name = get_class($model);

        $class_display_name = "";
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
                else
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
}
