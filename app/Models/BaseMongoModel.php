<?php

namespace App\Models;

use App\Helpers\FileUtility;
use App\Traits\CacheTrait;
use App\Traits\StorageTrait;
use Illuminate\Support\Facades\Auth;
use MongoDB\Laravel\Eloquent\Builder;
use MongoDB\Laravel\Eloquent\Model;

class BaseMongoModel extends Model implements BaseModelInterface
{
    use StorageTrait, CacheTrait;

    protected $connection = 'mongodb';

    /**
     * name of table fields which uniquly identify the record
     */
    protected static array $unique_fields = [], $file_fields = [];

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
        foreach ($this->getAttributes() as $field => $value) {
            if (is_null($value)) {
                continue;
            }

            if (is_string($value) && strlen($value) > 0) {
                $this->{$field} = utf8_encode($value);
            }
        }
    }

    public function beforeDelete() {}

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

    public function getFileFields()
    {
        return static::$file_fields;
    }


    public static function parseValueBeforeUseInDBQuery($field, $value)
    {
        return $value;
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

    public function insertIgnoreIfExist(array $data)
    {
        $record = $this->getUniqueId($data);

        if (!$record) {
            $record = static::create($data);
        }

        return $record->id;
    }

    public function getUniqueId(array $data)
    {
        if (!static::$unique_fields) {
            throw_exception("unique_fields is not set yet");
        }

        $conditions = [];
        foreach (static::$unique_fields as $unique_field) {
            if (!isset($data[$unique_field])) {
                throw_exception("field $unique_field missing in argument array");
            }

            $conditions[] = [$unique_field, '=', self::parseValueBeforeUseInDBQuery($unique_field, $data[$unique_field])];
        }

        $count = static::where($conditions)->count();

        if ($count == 0) {
            return false;
        }

        if ($count > 1) {
            throw_exception("more than 1 records found");
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
}
