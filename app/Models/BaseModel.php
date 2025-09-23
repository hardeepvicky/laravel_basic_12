<?php

namespace App\Models;

<<<<<<< HEAD
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BaseModel extends Model
{
    /**
     * name of table fields which uniquly identify the record
     */
    protected static array $unique_fields = [];
=======
use App\Events\ModelDelete;
use App\Events\ModelInsert;
use App\Events\ModelUpdate;
use App\Exceptions\UserException;
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
>>>>>>> base_v3

    /**
     * set extra relationship array to overcome problem of accidential delete
     * this variable used in Controller.php -> delete()
     */
<<<<<<< HEAD
    public array $child_model_class = [];

    protected $guarded = ["id"];

    private static $tableInfo = [];

    public static function classDisplayName() : string
    {
        $class_name = str_class_name_to_human_text(static::class);
        
        return $class_name;
    }

=======
    public static array $child_model_class = [];

    protected static array $unique_fields = [];

    protected static array $file_fields = [];

    protected static array $list_fields = ["id", "name"];

    protected static array $fields_containg_tags = [];

    public array $custom_errors = [];

    protected $guarded = ["id"];

>>>>>>> base_v3
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
<<<<<<< HEAD
        });

        self::created(function ($model) {
            $model->forgotCache();
=======

            $model->setDefaultAttributes();

            $model->beforeSave();
        });

        self::created(function ($model) {
            $model->afterSave();
            event(new ModelInsert($model));
>>>>>>> base_v3
        });

        self::updating(function ($model) {

            if (static::tableHaveField($model->getTable(), 'updated_by')) {
                $auth_user = Auth::user();
                if ($auth_user) {
<<<<<<< HEAD
                    $model->updated_by = $auth_user->id;                    
                }
            }
        });

        self::updated(function ($model) {
            $model->forgotCache();
            
        });

        self::deleting(function ($model) {

        });

        self::deleted(function ($model) {
            $model->forgotCache();           
        });
    }

    public static function getTableInfo($table_name)
    {
        if (!isset(static::$tableInfo[$table_name])) {
            static::setTableInfo($table_name);
        }

        return static::$tableInfo[$table_name];
    }

    public static function setTableInfo($table_name)
    {
        $cache_key = "table-info-" . $table_name;

        $table_info = Cache::get($cache_key);

        if (!$table_info) {
            $table_info = [
                "columns" => []
            ];

            $columns = DB::select('SHOW COLUMNS FROM ' . $table_name);

            foreach ($columns as $column) {
                $table_info['columns'][$column->Field] = (array) $column;                
            }

            Cache::put($cache_key, $table_info);
        }

        static::$tableInfo[$table_name] = $table_info;
    }

    public static function tableHaveField($table_name, $field)
    {
        if (!isset(static::$tableInfo[$table_name])) {
            static::setTableInfo($table_name);
        }

        $info = static::$tableInfo[$table_name];

        if (isset($info["columns"][$field]))
        {
            return true;
        }

        return false;
    }
    
    public static function getModelCacheKey() : string
    {
        $str = get_called_class();
        $arr = explode("\\", $str);
        $str = end($arr);
        $str = trim(preg_replace('/(?<!\ )[A-Z]/', ' $0', $str));

        $key = get_cache_prefix() . "-" . $str;

        return  static::optimizeCacheKey($key);
    }

    public static function optimizeCacheKey($key) : string
    {
        $key = str_replace(".", "-", $key);

        $key = preg_replace('!\s+!', '-', $key);

        $key = trim($key, "-");
        
        $key = trim($key);

        return $key;
    }

    public static function getCache($key)
    {
        $model_cache_key = static::getModelCacheKey();

        $cache_key = $model_cache_key . "-" . $key;

        if (Cache::has($cache_key))
        {
            return Cache::get($cache_key);
        }

        return null;
    }

    public static function addCache($key, $data)
    {
        $model_cache_key = static::getModelCacheKey();

        $cache_key = $model_cache_key . "-" . static::optimizeCacheKey($key);

        //put cache
        Cache::put($cache_key, $data, laravel_constant("cache_time.model"));

        //make refrence to cache keys 
        $list_of_cache_keys = [];
        if (Cache::has($model_cache_key))
        {
            $list_of_cache_keys = Cache::get($list_of_cache_keys);
        }

        if (!in_array($cache_key, $list_of_cache_keys))
        {
            $list_of_cache_keys[] = $cache_key;
        }

        if (!Cache::put($model_cache_key, $list_of_cache_keys, laravel_constant("cache_time.model")))
        {
            throw_exception("Fail To Put Cache");
        }
    }

    protected function forgotCache()
    {
        $model_cache_key = static::getModelCacheKey();
        
        if (Cache::has($model_cache_key))
        {
            $list_of_cache_keys = Cache::get($model_cache_key);

            foreach($list_of_cache_keys as $cache_key)
            {
                Cache::forget($cache_key);
            }

            Cache::forget($model_cache_key);
        }
    }

    public static function parseValueBeforeUseInDBQuery($field, $value)
    {
        return $value;
    }

    public static function fetchList(Builder $builder, String $key_field, String $value_field, String $table_name)
    {    
        if (self::tableHaveField($table_name, $key_field) && self::tableHaveField($table_name, $value_field))   
        {
            return $builder->pluck($value_field, $key_field)->toArray();
        }

        $records = $builder->get();

        // d($value_field); dd($records->toArray());

        $list = [];

        foreach($records as $record)
        {
            $arr = $record->toArray();            
            $list[$arr[$key_field]] = $arr[$value_field];
=======
                    $model->updated_by = $auth_user->id;
                }
            }

            $model->beforeSave();
        });

        self::updated(function ($model) {
            $model->afterSave();
            event(new ModelUpdate($model));
        });

        self::deleting(function ($model) {
            $model->beforeDelete();
            event(new ModelDelete($model));
        });

        self::deleted(function ($model) {
            $model->forgotCache();
        });
    }

    public function beforeSave()
    {
        if ($this->custom_errors) {
            throw new UserException("Some errors occurs while saving", UserException::ERROR_USER_DATA_WRONG, [
                "custom_errors" => $this->custom_errors
            ]);
        }
    }

    public function afterSave()
    {
        $this->forgotCache();

        $this->saveAutoTags();

        $this->saveModuleLinksFromString();
    }

    public function saveModuleLinksFromString()
    {
        foreach(static::$fields_containg_tags as $field)
        {
            if ($this->{$field})
            {
                ModuleLink::saveLinksFromStringPattern($this, $field, $this->{$field});
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

        $this->convertTimeZoneForDisplay();
    }

    public function parseTagsForModuleLinking()
    {
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

        $list_fields = static::getListFields();

        if (!in_array($key_field, $list_fields) && static::tableHaveField($table_name, $key_field)) {
            $list_fields[] = $key_field;
        }

        if (!in_array($value_field, $list_fields) && static::tableHaveField($table_name, $value_field)) {
            $list_fields[] = $value_field;
        }

        $builder->select($list_fields);

        $records = $builder->get();

        // d($records->toArray());

        $list = [];

        foreach ($records as $record) {
            $arr = $record->toArray();
            if (isset($arr[$key_field]) && is_scalar($arr[$key_field]))
            {
                $list[$arr[$key_field]] = $arr[$value_field] ?? null;
            }
>>>>>>> base_v3
        }

        return $list;
    }

<<<<<<< HEAD
    public static function getList(String $key_field = "id", String $value_field = "display_name", $conditions = [], $order_by = "name", $order_dir = "asc")
    {
        $builder = static::query();

        $model = new static();
        $table_name = $model->getTable();

        $builder->orWhere(function ($query) use ($conditions) {
            if (isset($conditions['or_id']))
            {
                $v = $conditions['or_id'];

                if (is_array($v))
                {
                    if (!empty($v))
                    {
                        $query->whereIn("id", $v);
                    }
                }
                else
                {
                    $query->where("id", $v);
                }
            }
        });

        unset($conditions['or_id']);

        if (static::tableHaveField($table_name, 'is_active'))
        {
            if (!isset($conditions['is_active']))
            {
                $conditions['is_active'] = 1;
            }
        }

        $builder->orWhere(function ($query) use ($conditions) {
            foreach($conditions as $k => $v)
            {
                if (is_array($v))
                {
                    if (!empty($v))
                    {
                        $query->whereIn($k, $v);
                    }
                }
                else
                {
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

        if ($list)
        {
            return $list;
        }

        $builder = static::query();

        if ($order_by && $order_dir)
        {
            $builder->orderBy($order_by, $order_dir);
        }

        $model = new static();
        $table_name = $model->getTable();

        $list = static::fetchList($builder, $id, $value, $table_name);

        self::addCache($key, $list);

        return $list;
=======
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

    public static function getListFields()
    {
        return static::$list_fields;
    }

    public static function getUniqueFields()
    {
        return static::$unique_fields;
>>>>>>> base_v3
    }

    /*------------------------------------------------------------------*/
    /**------------------------Member Functions ----------------------- */
    /*------------------------------------------------------------------*/

<<<<<<< HEAD
=======
    public function setDefaultAttributes() {}

>>>>>>> base_v3
    public function hasAttribute($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    public function getClassDisplayName()
    {
        $class_name = static::classDisplayName();
<<<<<<< HEAD
        
        return $class_name;
    }

    public function getFilePath()
    {
        return "files/" . str_class_name_without_namespace(get_class($this)) . "/";
    }

    public function getFilePathForRecord()
    {
        return $this->getFilePath() . $this->id . "/";
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
            if ($model->isDirty())
            {
                if ( $model->save() )
                {
                    $is_update = true;
                }
                else
                {
                    $is_update = false;
                    throw new \Exception("Fail To Save");
                }
            }
        } else {
            $model = static::create($data);

            if ($model)
            {
                $is_insert = true;
            }
            else
            {
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

=======

        return $class_name;
    }

>>>>>>> base_v3
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
<<<<<<< HEAD
}
=======

    public function safeDelete(Model $model)
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

        foreach (static::$child_model_class as $childClass => $arr)
        {
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
                        return false;
                    }
                }
            } else {
                foreach ($foreign_fields as $foreign_field) {
                    $child_records = $childClass::where($foreign_field, $model->id)->get();

                    if ($child_records->count() > 0) {
                        foreach ($child_records as $child_record) {
                            $result = $model->safeDelete($child_record);

                            if (!$result)
                            {
                                return false;
                            }
                        }
                    }
                }
            }
        }

        if ($model->delete())
        {
            return true;
        }

        return false;
    }
}


>>>>>>> base_v3
