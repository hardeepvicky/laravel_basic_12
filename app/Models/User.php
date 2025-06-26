<?php

namespace App\Models;

use App\Acl\AccessControl;
use App\Helpers\FileUtility;
use App\Helpers\Util;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements BaseModelInterface
{
    use HasApiTokens, HasFactory, Notifiable, StorageTrait, CacheTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'whatsapp_mobile',
        'password',
        'profile_image',
        'dont_send_email',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'display_name',
    ];

    protected static array $unique_fields = ["email"];

    protected static array $file_fields = ["profile_image"];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'used_in_other_table_created_by' => 'array',
        'used_in_other_table_updated_by' => 'array',
    ];

    public array $child_model_class = [
        UserRole::class => [
            "foreignKey" => "user_id",
            "preventDelete" => false,
            "label" => "User's Role"
        ],
    ];

    
    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {

            if (Schema::hasColumn($model->getTable(), "created_by")) {
                $auth_user = Auth::user();
                if ($auth_user) {
                    $model->created_by = $auth_user->id;
                }

                $model->uid = self::generateUID();
            }
        });

        self::created(function ($model) {
            $model->forgotCache();
        });

        self::updating(function ($model) {

            if (Schema::hasColumn($model->getTable(), "updated_by")) {
                $auth_user = Auth::user();
                if ($auth_user) {
                    $model->updated_by = $auth_user->id;
                }
            }

            if (!$model->uid) {
                $model->uid = self::generateUID();
            }
        });

        self::updated(function ($model) {
            $model->forgotCache();
        });

        self::deleting(function ($model) {});

        self::deleted(function ($model) {
            $model->forgotCache();
        });
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

    public function getFileFields()
    {
        return static::$file_fields;
    }

    protected function getDisplayNameAttribute()
    {
        return $this->name;
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function userRole()
    {
        return $this->hasMany(UserRole::class, 'user_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, UserRole::class);
    }

    public function getUniqueValidationRules()
    {
        $field_rules = [
            "email" => [Rule::unique($this->connection . "." . $this->getTable(), "email")],
            "mobile" => [Rule::unique($this->connection . "." . $this->getTable(), "mobile")],
        ];

        if ($this->id) {
            foreach ($field_rules as $field => $rules) {
                foreach ($rules as $rule) {
                    if ($rule instanceof Unique) {
                        $rule->ignore($this->id, $this->primaryKey);
                    }
                }
            }
        }

        return $field_rules;
    }

    public function isRquireOTPVerificationOnLogin()
    {
        if (!$this->dont_send_email) {
            return false;
        }
    }

    public function assignRoles(array $new_role_id_list)
    {
        $user_id = $this->id;

        $roles = Role::find($new_role_id_list);

        $this->roles()->sync($roles);

        $accessControl = AccessControl::init();
        $accessControl->clearMenuCache([$user_id]);
    }

    public function getRolesList()
    {
        $list = [];

        foreach ($this->roles as $role) {
            $arr = $role->toArray();
            $list[$arr['id']] = $arr['display_name'];
        }

        return $list;
    }

    public function hasRole($role_id_code_name)
    {
        $role = $this->roles()->where(function($q) use ($role_id_code_name){
            $q->orWhere("roles.code", $role_id_code_name)->orWhere("roles.name", $role_id_code_name);
        })->first();

        if ($role)
        {
            return true;
        }

        return false;
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
        $auth_user = Auth::user();

        if ($auth_user && $auth_user->id == $this->id) {
            throw new \Exception("You can not de-activate your self");
        }

        $this->is_active = 0;
        $this->save();
    }

    public function getClassDisplayName()
    {
        $class_name = static::classDisplayName();

        return $class_name;
    }

    public static function classDisplayName(): string
    {
        $class_name = str_class_name_to_human_text(static::class);

        return $class_name;
    }

    public static function fetchList(Builder $builder, String $key_field, String $value_field, String $table_name)
    {
        if (Schema::hasColumn($table_name, $key_field) && Schema::hasColumn($table_name, $value_field)) {
            return $builder->pluck($value_field, $key_field)->toArray();
        }

        $records = $builder->get();

        $list = [];

        foreach ($records as $record) {
            if ($value_field == "display_name_with_id") {
                $list[$record->{$key_field}] = $record->display_name . "-" . $record->id;
            } else {
                $list[$record->{$key_field}] = $record->{$value_field};
            }
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

    public static function getListCache(String $key_field = "id", String $value_field = "display_name_with_id", $order_by = "name", $order_dir = "ASC")
    {
        $model = new static();
        $table_name = $model->getTable();

        $key = "list-" . $key_field . "-" . $value_field;

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

        $list = static::fetchList($builder, $key_field, $value_field, $table_name);

        self::addCache($key, $list);

        return $list;
    }

    private function _updateUsageJson($json, $table, $label, $counter, $extra = [])
    {
        if (!is_array($json)) {
            $json = [];
        }

        $is_found = false;
        foreach ($json as $k => $arr) {
            if (isset($arr['table']) && $arr['table'] == $table) {
                if (!isset($arr['counter'])) {
                    $arr['counter'] = 0;
                }

                $arr['counter'] += $counter;

                if ($arr['counter'] < 0) {
                    $arr['counter'] = 0;
                }

                $arr = array_merge($arr, $extra);

                $json[$k] = $arr;

                $is_found = true;
            }
        }

        if (!$is_found && $counter >= 0) {
            $arr = array_merge([
                "table" => $table,
                "label" => $label,
                "counter" => $counter
            ], $extra);

            $json[] = $arr;
        }

        return $json;
    }

    public function changeCreatedByUsage(BaseModel $model, $counter)
    {
        $json = $this->used_in_other_table_created_by;

        $this->used_in_other_table_created_by = $this->_updateUsageJson($json, $model->getTable(), $model->getClassDisplayName(), $counter, [
            "last_id" => $model->id
        ]);

        $this->save();
    }

    public function changeCreatedByUsageBulk(array $models)
    {
        $json = $this->used_in_other_table_created_by;

        foreach ($models as $model) {
            if ($model instanceof Model) {
                $this->used_in_other_table_created_by = $this->_updateUsageJson($json, $model->getTable(), $model->getClassDisplayName(), 1, [
                    "last_id" => $model->id
                ]);
            }
        }

        $this->save();
    }

    public function changeUpdatedByUsage(BaseModel $model, $counter)
    {
        $json = $this->_parseJsonBeforeUpdateByUsage($model, $this->used_in_other_table_updated_by);

        $this->used_in_other_table_updated_by = $this->_updateUsageJson($json, $model->getTable(), $model->getClassDisplayName(), $counter, [
            "last_id" => $model->id
        ]);

        $this->save();
    }

    private function _parseJsonBeforeUpdateByUsage(BaseModel $model, $json)
    {
        if ($json && is_array($json)) {
            $model_table = $model->getTable();

            foreach ($json as $k => $arr) {
                if (isset($arr['table']) && $arr['table'] == $model_table) {
                    if (isset($arr['last_id']) && $arr['last_id']) {
                        if ($arr['last_id'] == $model->id) {
                            $arr['counter'] -= 1;
                        }
                    }
                }

                $json[$k] = $arr;
            }
        }

        return $json;
    }

    public function changeUpdatedByUsageBulk(array $models)
    {
        $json = $this->used_in_other_table_updated_by;

        foreach ($models as $model) {
            if ($model instanceof Model) {
                $json = $this->_parseJsonBeforeUpdateByUsage($model, $json);
                $this->used_in_other_table_updated_by = $this->_updateUsageJson($json, $model->getTable(), $model->getClassDisplayName(), 1, [
                    "last_id" => $model->id
                ]);
            }
        }

        $this->save();
    }

    public function getProfileImage()
    {
        if ($this->profile_image) {
            return FileUtility::get($this->profile_image);
        }

        if ($this->avatar) {
            return $this->avatar;
        }

        return null;
    }

    public static function generateUID()
    {
        return Util::getRandomString(10) . time();
    }
}
