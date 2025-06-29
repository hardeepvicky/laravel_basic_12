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

class BaseModel extends Model implements BaseModelInterface
{
    use StorageTrait, CacheTrait, ModelListTrait, MysqlTableInfoTrait;

    protected static array $unique_fields = [];

    protected static array $file_fields = [];

    protected static array $list_fields = ["id", "name"];

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

            if (static::tableHaveField($model->getTable(), 'created_by')) {
                $auth_user = Auth::user();
                if ($auth_user) {
                    $model->created_by = $auth_user->id;
                }
            }

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

    public function afterFind() {}

    
    public static function fetchList(Builder $builder, String $key_field, String $value_field)
    {
        $model = new static();
        $table_name  = $model->getTable();

        if (!in_array($key_field, self::$list_fields) && self::tableHaveField($table_name, $key_field) )
        {
            self::$list_fields[] = $key_field;
        }

        if (!in_array($value_field, self::$list_fields) && self::tableHaveField($table_name, $value_field) )
        {
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

    /*------------------------------------------------------------------*/
    /**------------------------Member Functions ----------------------- */
    /*------------------------------------------------------------------*/

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
            throw new UserException("unique_fields is not set yet");
        }

        $conditions = [];
        foreach (static::$unique_fields as $unique_field) {
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
