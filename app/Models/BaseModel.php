<?php

namespace App\Models;

use App\Exceptions\UserException;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Traits\CacheTrait;
use App\Traits\CommonModelTrait;
use App\Traits\CommonMysqlModelTrait;
use App\Traits\ModelListTrait;
use App\Traits\MysqlTableInfoTrait;
use App\Traits\StorageTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BaseModel extends Model implements BaseModelInterface
{
    use StorageTrait, CacheTrait, ModelListTrait, CommonModelTrait, MysqlTableInfoTrait, CommonMysqlModelTrait;
    
    /**
     * set extra relationship array to overcome problem of accidential delete
     * this variable used in Controller.php -> delete()
     */
    public static array $child_model_class = [];

    protected static array $unique_fields = [];

    protected static array $file_fields = [];

    protected static array $list_fields = ["id", "name"];

    protected static array $fields_containg_tags = [];

    public array $custom_errors = [];

    protected $guarded = ["id"];

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
            $model->afterSave();
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
            $model->afterSave();
        });

        self::deleting(function ($model) {
            $model->beforeDelete();
        });

        self::deleted(function ($model) {            
            $model->forgotCache();
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

    public function afterSave()
    {
        $this->forgotCache();

        foreach(static::$fields_containg_tags as $field)
        {
            if ($this->{$field})
            {
                ModuleLink::saveLinksFromStringPattern($this, $this->{$field});
            }
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

        foreach(static::$fields_containg_tags as $field)
        {
            if ($this->{$field})
            {
                $this->{$field} = ModuleLink::parseStringToMakeLinks($this, $this->{$field});
            }
        }
    }

    public static function fetchList(Builder $builder, String $key_field, String $value_field)
    {
        $model = new static();
        $table_name  = $model->getTable();

        if (!in_array($key_field, static::$list_fields) && static::tableHaveField($table_name, $key_field)) {
            static::$list_fields[] = $key_field;
        }

        if (!in_array($value_field, static::$list_fields) && static::tableHaveField($table_name, $value_field)) {
            static::$list_fields[] = $value_field;
        }

        $builder->select(static::$list_fields);

        $records = $builder->get();

        // dd($records); exit;

        $list = [];

        foreach ($records as $record) {
            $arr = $record->toArray();
            $list[$arr[$key_field]] = $arr[$value_field];
        }

        return $list;
    }

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
    
    public static function getFileFields()
    {
        return static::$file_fields;
    }

    public static function getUniqueFields()
    {
        return static::$unique_fields;
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
