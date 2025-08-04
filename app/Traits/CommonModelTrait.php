<?php
namespace App\Traits;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\DateUtility;
use App\Helpers\LaravelExtend;
use App\Models\Tag;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
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
                    if (!$model->saveQuietly())
                    {
                        throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
                    }
                }
                else
                {
                    if (!$model->save())
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
                if (!$model->saveQuietly())
                {
                    throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
                }
                
            }
            else
            {
                if (!$model->save())
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

        if (!$unique_fields) {
            throw new UserException("unique_fields is not set yet");
        }

        $conditions = [];
        foreach ($unique_fields as $unique_field) {
            if (!isset($data[$unique_field])) {
                throw new UserException("field : $unique_field missing in argument data");
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

        LaravelExtend::addError($label, $messages, $group, $extra);
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

    public function convertTimeZoneBeforeQueryToDatabase(array $data = [])
    {
        if (is_null(DateUtility::$login_user_time_zone))
        {
            return;
        }

        $date_fields = [];

        foreach($this->casts as $field => $config)
        {
            if (strpos($config, "date") !== false)
            {
                $date_fields[$field] = $config;
            }
        }

        if (method_exists($this, "getTableInfo"))
        {
            $table_info = $this->getTableInfo($this->getTable());

            foreach($table_info['columns'] as $field => $field_arr)
            {
                if (!isset($date_fields[$field]))
                {
                    if (in_array($field_arr['Type'], ["timestamp", "date", "datetime"]))
                    {
                        $date_fields[$field] = "datetime";
                    }
                }
            }
        }

        if (empty($data))
        {
            foreach($date_fields as $field => $config)
            {
                if ($this->{$field})
                {
                    if ($this->{$field} instanceof Carbon)
                    {
                        if ($this->{$field}->timezoneName != "UTC")
                        {
                            $this->{$field}->setTimezone("UTC");
                        }
                    }
                    else if ($this->{$field} instanceof DateTime)
                    {
                        $timezoneObject = $this->{$field}->getTimezone();

                        $timezoneName = $timezoneObject->getName();

                        if ($timezoneName != "UTC")
                        {
                            $this->{$field}->setTimezone(new DateTimeZone("UTC"));
                        }
                    }
                    else
                    {
                        //consider as user input
                        $new_field = DateUtility::getDateObj($this->{$field}, DateUtility::$login_user_time_zone);
                        $new_field->setTimezone(new DateTimeZone("UTC"));
                        $this->{$field} = $new_field->format('c');
                    }
                }
            }
        }
        else
        {
            foreach($date_fields as $field => $config)
            {
                if (isset($data[$field]))
                {
                    if ($data[$field] instanceof Carbon)
                    {
                        if ($data[$field]->timezoneName != "UTC")
                        {
                            $data[$field]->setTimezone("UTC");
                        }
                    }
                    else if ($data[$field] instanceof DateTime)
                    {
                        $timezoneObject = $data[$field]->getTimezone();

                        $timezoneName = $timezoneObject->getName();

                        if ($timezoneName != "UTC")
                        {
                            $data[$field]->setTimezone(new DateTimeZone("UTC"));
                        }
                    }
                    else
                    {
                        //consider as user input

                        $new_field = DateUtility::getDateObj($data[$field], DateUtility::$login_user_time_zone);
                        $new_field->setTimezone(new DateTimeZone("UTC"));
                        $data[$field] = $new_field->format('c');
                    }
                }
            }

            return $data;
        }
    }

    public function convertTimeZoneForDisplay(array $data = [], array $date_fields = [])
    {
        if (is_null(DateUtility::$login_user_time_zone))
        {
            return;
        }

        foreach($this->casts as $field => $config)
        {
            if (strpos($config, "date") !== false || strpos($config, "timestamp") !== false)
            {
                $date_fields[$field] = $config;
            }
        }

        if (method_exists($this, "getTableInfo"))
        {
            $table_info = $this->getTableInfo($this->getTable());

            foreach($table_info['columns'] as $field => $field_arr)
            {
                if (!isset($date_fields[$field]))
                {
                    if (in_array($field_arr['Type'], ["timestamp", "date", "datetime"]))
                    {
                        $date_fields[$field] = $field_arr['Type'];
                    }
                }
            }
        }
        else
        {
            $date_fields['created_at'] = 'timestamp';
            $date_fields['updated_at'] = 'timestamp';
        }

        if (empty($data))
        {
            foreach($date_fields as $field => $date_config)
            {
                if ($this->{$field})
                {
                    $original_field = $field . "_original";

                    $new_field = $field . "_user_tz";

                    if ($this->{$field} instanceof DateTime || $this->{$field} instanceof Carbon)
                    {
                        $this->{$original_field} = $this->{$field}->clone();

                        $timezoneObject = $this->{$field}->getTimezone();

                        $timezoneName = $timezoneObject->getName();

                        if ($timezoneName != DateUtility::$login_user_time_zone->getName())
                        {
                            $this->{$field} = $this->{$new_field} = $this->{$field}->setTimezone(DateUtility::$login_user_time_zone);
                        }                   
                    }
                    else
                    {
                        $format = 'Y-m-d H:i:s T';

                        if (strpos($date_config, ":") !== false)
                        {
                            $format = substr($date_config, strpos($date_config, ":") + 1);
                        }

                        $this->{$original_field} = $this->{$field};

                        //consider as UTC
                        $new_date = DateUtility::getDateObj($this->{$field});
                        if ($new_date)
                        {
                            $new_date->setTimezone(DateUtility::$login_user_time_zone);
                            $this->{$field} = $new_date->format($format);
                        }
                    }
                }
            }
        }
        else
        {
            foreach($date_fields as $field => $date_config)
            {
                if (isset($data[$field]) && $data[$field])
                {
                    $original_field = $field . "_original";
                    
                    if ($data[$field] instanceof DateTime || $data[$field] instanceof Carbon)
                    {
                        $data[$original_field] = $data[$field]->clone();

                        $timezoneObject = $data[$field]->getTimezone();

                        $timezoneName = $timezoneObject->getName();

                        if ($timezoneName != DateUtility::$login_user_time_zone->getName())
                        {
                            $data[$field] = $data[$field]->setTimezone(DateUtility::$login_user_time_zone);
                        }
                    }
                    else
                    {
                        $format = 'Y-m-d H:i:s T';

                        if (strpos($date_config, ":") !== false)
                        {
                            $format = substr($date_config, strpos($date_config, ":") + 1);
                        }

                        $data[$original_field] = $data[$field];
                        //consider as UTC
                        $new_date = DateUtility::getDateObj($data[$field]);
                        if ($new_date)
                        {
                            $new_date->setTimezone(DateUtility::$login_user_time_zone);
                            $data[$field] = $new_date->format($format);
                        }
                    }
                }
            }

            return $data;
        }
    }

    public function saveAutoTags()
    {
        if (!isset($this->field_for_create_tag) || empty($this->field_for_create_tag))
        {
            return;
        }

        $save_data = [
            "main_table_field" => $this->field_for_create_tag,
            "main_id" => $this->id,
        ];

        if ($this->field_for_create_tag == "display_name")
        {
            $save_data['name'] = $this->getDisplayName();
        }
        else
        {
            $save_data['name'] = $this->{$this->field_for_create_tag};
        }

        Tag::createTag($save_data, $this);
    }

    public static function getChildIdList(array $parent_id_list, string $parent_field = "parent_id")
    {
        $qb = self::query();
        $qb->whereIn($parent_field, $parent_id_list);

        $id_list = $qb->pluck("id")->toArray();

        if ($id_list)
        {
            $id_list = array_merge($id_list, self::getChildIdList($id_list));
        }

        return $id_list;
    }
}