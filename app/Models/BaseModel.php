<?php

namespace App\Models;

use App\Exceptions\UserException;
use App\Traits\CacheTrait;
use App\Traits\ModelListTrait;
use App\Traits\MysqlTableInfoTrait;
use App\Traits\StorageTrait;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use ReflectionClass;

class BaseModel extends Model implements BaseModelInterface
{
    use StorageTrait, CacheTrait, ModelListTrait, MysqlTableInfoTrait;

    protected static array $unique_fields = [];

    protected static array $file_fields = [];

    protected static array $list_fields = ["id", "name"];

    public array $custom_errors = [];

    /**
     * set extra relationship array to overcome problem of accidential delete
     * this variable used in Controller.php -> delete()
     */
    public array $child_model_class = [];

    protected $guarded = ["id"];

    public static function classDisplayName(): string
    {
        $class_name = str_class_name_to_human_text(static::class);

        return $class_name;
    }

    public static function getCacheKey(): string
    {
        $str = static::class;

        return static::optimizeCacheKey($str);
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {

            if (static::tableHaveField($model->getTable(), 'created_by')) {
                $auth_user = Auth::user();
                if ($auth_user) {
                    $model->created_by = $auth_user->id;
                }
            }

            $model->setDefaultAttributes();

            $model->beforeSave();
        });

        self::created(function ($model) {
            $model->forgotCache();
        });

        self::updating(function ($model) {

            if (static::tableHaveField($model->getTable(), 'updated_by')) {
                $auth_user = Auth::user();
                if ($auth_user) {
                    $model->updated_by = $auth_user->id;
                }
            }

            $model->beforeSave();
        });

        self::updated(function ($model) {
            $model->forgotCache();
        });

        self::deleting(function ($model) {
            $model->beforeDelete();
        });

        self::deleted(function ($model) {
            $model->forgotCache();
        });
    }

    public function getFileFields()
    {
        return static::$file_fields;
    }

    public function beforeSave()
    {
        if ($this->custom_errors) {
            throw new UserException("Some errors in model while saving", UserException::ERROR_USER_DATA_WRONG, [
                "custom_errors" => $this->custom_errors
            ]);
        }

        foreach ($this->getAttributes() as $field => $value) {
            if (is_null($value)) {
                continue;
            }

            if (is_string($value) && strlen($value) > 0) {
                $this->{$field} = utf8_encode($value);
            }
        }

        // dd($this->getAttributes());
    }

    public function beforeDelete() {}

    public function afterFind() {}

    public function getFieldLabels()
    {
        $labels = [];

        $table_info = $this->getTableInfo($this->getTable());

        // d($table_info, true);

        foreach ($table_info['columns'] as $field => $field_attr) {

            $text = str_replace(["_id", "is_", "_at"], "", $field);

            $labels[$field] = ucwords(str_function_name_to_human_text($text));
        }

        // d($labels, true);

        return $labels;
    }


    public static function fetchList(Builder $builder, String $key_field, String $value_field)
    {
        $model = new static();
        $table_name  = $model->getTable();

        if (!in_array($key_field, self::$list_fields) && self::tableHaveField($table_name, $key_field)) {
            self::$list_fields[] = $key_field;
        }

        if (!in_array($value_field, self::$list_fields) && self::tableHaveField($table_name, $value_field)) {
            self::$list_fields[] = $value_field;
        }

        $builder->select(self::$list_fields);

        $records = $builder->get();

        // dd($records); exit;

        $list = [];

        foreach ($records as $record) {
            $arr = $record->toArray();
            $list[$arr[$key_field]] = $arr[$value_field];
        }

        return $list;
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

    /*------------------------------------------------------------------*/
    /**------------------------Member Functions ----------------------- */
    /*------------------------------------------------------------------*/

    public function setDefaultAttributes() {}

    public function hasAttribute($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    public function getClassDisplayName()
    {
        $class_name = static::classDisplayName();

        return $class_name;
    }

    public function getNextId()
    {
        $table = $this->getTable();
        $statement = DB::select("SHOW TABLE STATUS LIKE '" . $table . "'");
        $nextId = $statement[0]->Auto_increment;

        return $nextId;
    }

    public function insertOrUpdate(array $data, &$is_insert = null, &$is_update = null)
    {
        $model = $this->getUniqueId($data);

        if ($model) {
            $model->fill($data);
            if ($model->isDirty()) {
                if ($model->save()) {
                    $is_update = true;
                } else {
                    $is_update = false;
                    throw new \Exception("Fail To Save");
                }
            }
        } else {
            $model = static::create($data);

            if ($model) {
                $is_insert = true;
            } else {
                $is_insert = false;
                return null;
            }
        }

        return $model->id;
    }

    public function insertIgnoreIfExist(array $data, &$is_insert = null)
    {
        $record = $this->getUniqueId($data);

        // dump($record);

        if (!$record) {
            $record = static::create($data);
            if (!$record)
            {
                throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
            }

            $is_insert = true;
        }

        return $record->id;
    }

    public function getUniqueId(array $data)
    {
        if (!static::$unique_fields) {
            throw new UserException("unique_fields is not set yet");
        }

        $conditions = [];
        foreach (static::$unique_fields as $unique_field) {
            if (!isset($data[$unique_field])) {
                throw new UserException("field $unique_field missing in argument data");
            }

            if (isset($this->casts) && $this->casts)
            {

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

    public function activate()
    {
        $this->is_active = 1;
        $this->save();
    }

    public function deActivate()
    {
        $this->is_active = 0;
        $this->save();
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
