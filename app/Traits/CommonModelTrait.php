<?php
namespace App\Traits;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use ReflectionClass;
use ReflectionMethod;

trait CommonModelTrait
{
    public static function insertOrUpdate(array $data, &$is_insert = null, &$is_update = null, bool $save_quitely = false)
    {
        $model = self::getUniqueRecord($data);

        if ($model) {
            $model->fill($data);
            if ($model->isDirty()) {

                if ($save_quitely)
                {
                    if (!$model->save())
                    {
                        throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
                    }
                }
                else
                {
                    if (!$model->saveQuietly())
                    {
                        throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
                    }
                }
                $is_update = true;
                $is_insert = false;
            }
        } else {

            $model = new static();

            $model->fill($data);

            if ($save_quitely)
            {
                if (!$model->save())
                {
                    throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
                }
            }
            else
            {
                if (!$model->saveQuietly())
                {
                    throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
                }
            }

            $is_insert = true;
            $is_update = false;
        }

        return $model;
    }

    public static function insertIgnoreIfExist(array $data, &$is_insert = null, bool $save_quitely = false)
    {
        $model = self::getUniqueRecord($data);

        // dump($record);

        if (!$model) {

            $model = new static($data);

            if ($save_quitely)
            {
                if (!$model->saveQuietly()) {
                    throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
                }
            }
            else
            {
                if (!$model->save()) {
                    throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
                }
            }

            $is_insert = true;
        }
        else
        {
            $is_insert = false;
        }

        return $model;
    }

    public static function getUniqueRecord(array $data)
    {
        $unique_fields = self::getUniqueFields();

        if ($unique_fields) {
            throw new UserException("unique_fields is not set yet");
        }

        $conditions = [];
        foreach ($unique_fields as $unique_field) {
            if (!isset($data[$unique_field])) {
                throw new UserException("field $unique_field missing in argument data");
            }

            $conditions[] = [$unique_field, '=', $data[$unique_field]];
        }

        $count = static::where($conditions)->count();

        if ($count == 0) {
            return false;
        }

        if ($count > 1) {
            throw new UserException("more than 1 records found");
        }

        $record = static::where($conditions)->first(["id"]);

        return $record;
    }

    public static function getAllRelations(Model $model): array
    {
        $class = new ReflectionClass($model);
        $methods = $class->getMethods();

        $relations = [];
        foreach ($methods as $method) {
            if ($method->class != get_class($model) || $method->getNumberOfParameters() > 0) continue;

            try {
                $relation = $method->invoke($model);
                if ($relation instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                    $relations[$method->getName()] = [
                        "model" => $method,
                        "relation" => $relation,
                    ];
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return $relations;
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

    public function loadAllRelations(&$relations = [])
    {
        $class = new ReflectionClass($this);
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            // Skip inherited methods and methods with parameters
            if (
                $method->class !== get_class($this) ||
                $method->getNumberOfParameters() > 0
            ) {
                continue;
            }

            try {
                $return = $method->invoke($this);

                if ($return instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                    $relations[] = $method->getName();
                }
            } catch (\Throwable $e) {
                // Skip methods that throw exceptions
                continue;
            }
        }

        return $this->load($relations);
    }
}