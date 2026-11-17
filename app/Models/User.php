<?php

namespace App\Models;

use App\Acl\AccessControl;
use App\Events\ModelDelete;
use App\Events\ModelUpdate;
use App\Events\UserInsert;
use App\Exceptions\UserException;
use App\Helpers\FileUtility;
use App\Helpers\Util;
use App\Traits\CacheTrait;
use App\Traits\CommonModelTrait;
use App\Traits\CommonMysqlModelTrait;
use App\Traits\ModelListTrait;
use App\Traits\MysqlTableInfoTrait;
use App\Traits\StorageTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Laravel\Sanctum\HasApiTokens;
use ReflectionClass;

class User extends Authenticatable implements BaseModelInterface
{
    use HasApiTokens, HasFactory, Notifiable, StorageTrait, CacheTrait, CommonModelTrait, ModelListTrait, MysqlTableInfoTrait, CommonMysqlModelTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',                
        'name',        
        'email',
        'mobile',        
        'password',        
        'profile_image',
        'avatar',
        'dont_send_email',
        'is_active',        
        'is_secure_login',        
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

    public static array $child_model_class = [
        UserRole::class => [
            "foreignKey" => "user_id",
            "preventDelete" => false,
            "label" => "User's Role"
        ],
        Task::class => [
            "foreignKey" => "assign_to_user_id",
            "preventDelete" => true,
        ],
    ];

    protected static array $unique_fields = ["email"];

    protected static array $file_fields = ["profile_image"];

    protected static array $list_fields = ["id", "name", "type"];

    public const TYPE_INTERNAL = "internal";
    public const TYPE_EXTERNAL = "external";

    public const TYPE_LIST = [
        self::TYPE_INTERNAL => "Internal",
        self::TYPE_EXTERNAL => "External",
    ];


    public array $custom_errors = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {

            if (Schema::hasColumn($model->getTable(), "created_by")) {
                $auth_user = Auth::user();
                if ($auth_user) {
                    $model->created_by = $auth_user->id;
                }
            }
            
            $model->uid = self::generateUID();
        });

        self::created(function ($model) {
            $model->afterSave();

            event(new UserInsert($model));
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
            $model->afterSave();
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
    }

    public function beforeDelete()
    {
        if ($this->custom_errors) {
            throw new UserException("Some errors in model while deleting", UserException::ERROR_USER_DATA_WRONG, [
                "custom_errors" => $this->custom_errors
            ]);
        }
    }

    public function afterFind()
    {
        $fileUtility = new FileUtility(static::getStorageDisk());

        foreach (self::getFileFields() as $field) {
            if (isset($this->{$field}) && $this->{$field}) {
                $field_url_field = $field . "_url";

                $this->{$field_url_field} = $fileUtility->getUrl($this->{$field});
            }
        }

        $this->convertTimeZoneForDisplay();
    }


    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->name . "-" . $this->id;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, UserRole::class);
    }

    public function userRole()
    {
        return $this->hasMany(UserRole::class, 'user_id');
    }

    public function userCategory()
    {
        return $this->belongsTo(DropDownValue::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assign_to_user_id');
    }

    public function recuringTasks()
    {
        return $this->hasMany(RecurringTask::class, 'assign_to_user_id');
    }

    /**
     * Get the entity's notifications.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function notifications(): MorphMany
    {
        // Tell Laravel to use your custom Notification model for this relationship
        return $this->morphMany(Notification::class, 'notifiable')->orderBy('created_at', 'desc'); // Optional: order them
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "email" => [Rule::unique($this->connection . "." . $this->getTable(), "email")],
        ];

        $rule = Rule::unique($this->connection . "." . $this->getTable(), "mobile");
        $rule->whereNotNull("mobile");
        $field_rules['mobile'] = [$rule];

        if ($this->id) {
            foreach ($field_rules as $field => $rules) {
                foreach ($rules as $rule) {
                    if ($rule instanceof Unique) {
                        // d([$field => $this->id]);
                        $rule->ignore($this->id, $this->primaryKey);
                    }
                }
            }
        }

        return $field_rules;
    }

    public function getRequiredIfRules(Request $request)
    {
        $field_rules = [];

        $field_rules["name"] = ["required"];

        return $field_rules;
    }

    public static function getFileFields()
    {
        return static::$file_fields;
    }

    public static function getUniqueFields()
    {
        return static::$unique_fields;
    }

    public function isRquireOTPVerificationOnLogin()
    {
        if ($this->is_secure_login) {
            return true;
        }

        return false;
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
        $role = $this->roles()->where(function ($q) use ($role_id_code_name) {
            $q->orWhere("roles.code", $role_id_code_name)->orWhere("roles.name", $role_id_code_name);
        })->first();

        if ($role) {
            return true;
        }

        return false;
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
            throw new UserException("You can not de-activate your self", UserException::ERROR_USER_ACTION_RESTRICT);
        }

        $this->is_active = 0;
        $this->save();
    }

    public function getClassDisplayName()
    {
        return self::classDisplayName();
    }

    public static function getCacheKey(): string
    {
        $str = "User";

        return static::optimizeCacheKey($str);
    }

    public static function classDisplayName(): string
    {
        $class_name = "User";

        return $class_name;
    }

    public static function fetchList(Builder $builder, String $key_field, String $value_field)
    {
        $model = new static();
        $table_name  = $model->getTable();

        if (!in_array($key_field, self::$list_fields) && static::tableHaveField($table_name, $key_field)) {
            self::$list_fields[] = $key_field;
        }

        if (!in_array($value_field, self::$list_fields) && static::tableHaveField($table_name, $value_field)) {
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


    public function getProfileImage($defalut = "img/user.png")
    {
        if (isset($this->profile_image_url) && $this->profile_image_url) {
            return $this->profile_image_url;
        }

        if ($this->profile_image) {
            $fileUtility = new FileUtility($this->getStorageDisk());
            $url = $fileUtility->getUrl($this->profile_image);

            if ($url) {
                return $url;
            }

            return "/" . NOT_FOUND_IMAGE_PATH;
        }

        if ($this->avatar) {
            return $this->avatar;
        }

        return $defalut;
    }

    public static function generateUID()
    {
        return Util::getRandomString(10) . time();
    }

    public function setPassword($plain_password)
    {
        $this->password = bcrypt($plain_password);
    }
}
