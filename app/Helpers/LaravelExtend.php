<?php

namespace App\Helpers;

use App\Jobs\Backend\JobForgotPassword;
use App\Jobs\Backend\JobSendEmailUserOtp;
use App\Jobs\Backend\JobSendEmailOnRegisration;
use App\Models\BaseModelInterface;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use PDOException;
use ReflectionClass;
use Illuminate\Support\Str;

class LaravelExtend
{
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

        $link =  config("url") . "/otp_verify/" . $user->uid;

        $job = new JobSendEmailUserOtp($user->name, $user->email, $user->otp, $link);

        dispatch($job);
    }

    public static function send_whatsapp_user_otp(User $user)
    {
        $user->otp = Util::getRandomString(6, "1234567890");
        $user->otp_sent_datetime = date("Y-m-d H:i:s");

        if (!$user->save()) {
            throw_exception("User Save Failed");
        }

        $link =  config("url") . "/otp_verify/" . $user->uid;

        $job = new JobSendEmailUserOtp($user->name, $user->email, $user->otp, $link);

        dispatch($job);
    }

    public static function send_email_on_registeration(User $user)
    {
        $job = new JobSendEmailOnRegisration($user->name, $user->email);

        dispatch($job);
    }

    public static function send_forgot_password_link(User $user)
    {
        if (!$user->uid) {
            throw new Exception("UId is not generated");
        }

        $link =  config("url") . "/create_password/" . $user->uid;

        $job = new JobForgotPassword($user->name, $user->email, $link);

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
}
