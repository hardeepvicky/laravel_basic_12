<?php

namespace App\Models;

use App\Exceptions\UserException;
use App\Helpers\FileUtility;
use App\Traits\CacheTrait;
use App\Traits\CommonModelTrait;
use App\Traits\StorageTrait;
use Illuminate\Support\Facades\Auth;
use MongoDB\Laravel\Eloquent\Builder;
use MongoDB\Laravel\Eloquent\Model;
use ReflectionClass;

class BaseMongoModel extends Model implements BaseModelInterface
{
    use StorageTrait, CacheTrait, CommonModelTrait;

    protected $connection = 'mongodb';

    /**
     * name of table fields which uniquly identify the record
     */
    protected static array $unique_fields = [], $file_fields = [];

    /**
     * set extra relationship array to overcome problem of accidential delete
     * this variable used in Controller.php -> delete()
     */
    public static array $child_model_class = [];

    public array $custom_errors = [];

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
            $auth_user = Auth::user();
            if ($auth_user) {
                $model->created_by = $auth_user->id;
            }

            $model->beforeSave();
        });

        self::created(function ($model) {
            $model->forgotCache();
        });

        self::updating(function ($model) {
            $auth_user = Auth::user();
            if ($auth_user) {
                $model->updated_by = $auth_user->id;
            }

            $model->beforeSave();
        });

        self::updated(function ($model) {
            $model->forgotCache();
        });

        self::deleting(function ($model) {
            $model->beforeSave();
        });

        self::deleted(function ($model) {
            $model->forgotCache();
        });

        self::retrieved(function ($model) {
            $model->afterFind();
        });
    }

    public function beforeSave()
    {
        if ($this->custom_errors) {
            throw new UserException("Some errors in model while saving", UserException::ERROR_USER_DATA_WRONG, [
                "custom_errors" => $this->custom_errors
            ]);
        }
    }

    public function beforeDelete() {
        if ($this->custom_errors) {
            throw new UserException("Some errors in model while deleting", UserException::ERROR_USER_DATA_WRONG, [
                "custom_errors" => $this->custom_errors
            ]);
        }
    }

    public function afterFind()
    {
        $fileUtility = new FileUtility(static::getStorageDisk());

        foreach ($this->getFileFields() as $field) {
            if (isset($this->{$field}) && $this->{$field}) {
                $field_url_field = $field . "_url";

                $this->{$field_url_field} = $fileUtility->getUrl($this->{$field});
            }
        }
    }

    public static function getFileFields()
    {
        return static::$file_fields;
    }

    public static function getUniqueFields()
    {
        return static::$unique_fields;
    }

    public static function fetchList(Builder $builder, String $key_field, String $value_field, String $table_name)
    {
        $records = $builder->get();

        $list = [];

        foreach ($records as $record) {
            $arr = $record->toArray();
            $list[$arr[$key_field]] = $arr[$value_field];
        }

        return $list;
    }

    public static function getList(String $key_field = "id", String $value_field = "display_name", $conditions = [], $order_by = "name", $order_dir = "asc")
    {
        $builder = static::query();

        $model = new static();
        $table_name = $model->getTable();

        $builder->orWhere(function ($query) use ($conditions) {
            if (isset($conditions['or_id'])) {
                $v = $conditions['or_id'];

                if (is_array($v)) {
                    if (!empty($v)) {
                        $query->whereIn("id", $v);
                    }
                } else {
                    $query->where("id", $v);
                }
            }
        });

        unset($conditions['or_id']);

        if (static::tableHaveField($table_name, 'is_active')) {
            if (!isset($conditions['is_active'])) {
                $conditions['is_active'] = 1;
            }
        }

        $builder->orWhere(function ($query) use ($conditions) {
            foreach ($conditions as $k => $v) {
                if (is_array($v)) {
                    if (!empty($v)) {
                        $query->whereIn($k, $v);
                    }
                } else {
                    $query->where($k, $v);
                }
            }
        });

        if ($order_by && $order_dir) {
            $builder->orderBy($order_by, $order_dir);
        }

        $list = static::fetchList($builder, $key_field, $value_field, $table_name);

        return $list;
    }

    public static function getListCache(String $id = "id", String $value = "display_name", $order_by = "name", $order_dir = "ASC")
    {
        $key = "list-" . $id . "-" . $value;

        $list = static::getCache($key);

        if ($list) {
            return $list;
        }

        $builder = static::query();

        if ($order_by && $order_dir) {
            $builder->orderBy($order_by, $order_dir);
        }

        $model = new static();
        $table_name = $model->getTable();

        $list = static::fetchList($builder, $id, $value, $table_name);

        self::addCache($key, $list);

        return $list;
    }

    /*------------------------------------------------------------------*/
    /**------------------------Member Functions ----------------------- */
    /*------------------------------------------------------------------*/

    /**
     * @return int The next ID.
     */
    public function getNextId()
    {
        return uniqid();
    }

    public function hasAttribute($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    public function getClassDisplayName()
    {
        $class_name = static::classDisplayName();

        return $class_name;
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
}
