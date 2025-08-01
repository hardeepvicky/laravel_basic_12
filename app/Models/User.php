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
        'sub_type',
        'user_category_id',
        'dept_id',
        'sub_dept_id',
        'desg_id',
        'country_id',
        'name',
        'person_name',
        'person_last_name',
        'email',
        'mobile',
        'whatsapp_mobile',
        'office_landline_no',
        'password',
        'plain_password',
        'profile_image',
        'avatar',
        'dont_send_email',
        'is_active',
        'created_via',
        'is_secure_login',
        'google_id',
        'apple_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'plain_password',
        'password',
        'remember_token',
    ];

    protected $appends = [
        'display_name',
        'full_name'
    ];


    public string $field_for_create_tag = "display_name";

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
        UserAllowIp::class => [
            "foreignKey" => "user_id",
            "preventDelete" => false,
            "label" => "User's Allow IP"
        ],
        Task::class => [
            "foreignKey" => "assign_to_user_id",
            "preventDelete" => true,
        ],
        TaskUser::class => [
            "foreignKey" => "task_id",
            "preventDelete" => true
        ],
    ];

    protected static array $unique_fields = ["email"];

    protected static array $file_fields = ["profile_image"];

    protected static array $list_fields = ["id", "name", "type", "sub_type", "person_name"];

    public const TYPE_INTERNAL = "internal";
    public const TYPE_EXTERNAL = "external";

    public const TYPE_LIST = [
        self::TYPE_INTERNAL => "Internal",
        self::TYPE_EXTERNAL => "Outside of Celec",
    ];

    public const CREATED_VIA_MANUAL_ADD = "manual_add";
    public const CREATED_VIA_REGISTRATION = "register";
    public const CREATED_VIA_GOOGLE = "google";
    public const CREATED_VIA_APPLE = "apple";

    public const CREATED_VIA_LIST = [
        self::CREATED_VIA_MANUAL_ADD => "Manual Add From Backend",
        self::CREATED_VIA_REGISTRATION => "Registeration Form",
        self::CREATED_VIA_GOOGLE => "Google",
        self::CREATED_VIA_APPLE => "Apple",
    ];

    public const SUB_TYPE_TEAM_MEMBER = "TEAM_MEMBER";
    public const SUB_TYPE_DEMO = "DEMO";
    public const SUB_TYPE_EXISTING_BUYER = "EXISTING_BUYER";
    public const SUB_TYPE_RESELLER = "RESELLER";
    public const SUB_TYPE_OEM = "OEM";
    public const SUB_TYPE_VENDOR = "VENDOR";
    public const SUB_TYPE_FEANCHISE = "FEANCHISE";
    public const SUB_TYPE_SUPPLIER = "SUPPLIER";

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

            event(new ModelUpdate($model));
        });

        self::deleting(function ($model) {
            event(new ModelDelete($model));
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

        foreach (Department::$child_model_class as $child_model_class => $arr) {
            if (isset($arr['foreignKey'])) {
                if (!is_array($arr['foreignKey'])) {
                    $arr['foreignKey'] = [$arr['foreignKey']];
                }

                foreach ($arr['foreignKey'] as $foreign_field) {
                    switch ($child_model_class) {
                        case Task::class:
                            $this->tasks()->update([
                                $foreign_field => $this->dept_id
                            ]);
                            break;
                        case RecurringTask::class:
                            $this->recuringTasks()->update([
                                $foreign_field => $this->dept_id
                            ]);
                            break;
                    }
                }
            }
        }
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
    }

    protected function getFullNameAttribute()
    {
        return trim($this->person_name . " " . ($this->person_last_name ?? ""));
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        $sub_type_list = Type::getCache("sub_type_list");

        if (!$sub_type_list) {
            $type_conditions = [
                "type" => Type::getType($this, "sub_type")
            ];

            $sub_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

            $cache_time = 60 * 60 * 24;

            Type::addCache("sub_type_list", $sub_type_list, $cache_time);
        }

        if ($this->type == self::TYPE_EXTERNAL) {

            $sub_title = "(External)";

            if (isset($sub_type_list[$this->sub_type])) {
                $sub_title = "(" . $sub_type_list[$this->sub_type] . ")";
            }

            return $this->full_name . " " . $sub_title;
        } else {

            $name = $this->name;

            if ($this->type == self::TYPE_EXTERNAL) {
                $name .= " (External)";
            } else {
                if ($this->person_name && strtolower($this->person_name) != strtolower($name)) {

                    if (strpos($this->person_name, " ") !== false) {
                        $sub_str = trim(substr($this->person_name, 0, strpos($this->person_name, " ")));
                    } else {
                        $sub_str = $this->person_name;
                    }

                    $name = $sub_str . " >> " . $this->name;
                }
            }

            $sub_title = "";

            if (isset($sub_type_list[$this->sub_type]) && $this->sub_type != self::SUB_TYPE_TEAM_MEMBER)
            {
                $sub_title = "(" . $sub_type_list[$this->sub_type] . ")";
            }
            
            if ($sub_title)
            {
                $name .= " " . $sub_title;
            }
            
            return $name;
        }
    }

    public function getFullMobileNo($field = "mobile")
    {
        $phonecode = $this->country?->phonecode ?? "";

        if ($phonecode) {
            return "+" . $phonecode . $this->{$field};
        }

        return $this->{$field};
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

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class, "dept_id");
    }

    public function subDepartment()
    {
        return $this->belongsTo(SubDepartment::class, "sub_dept_id");
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, "desg_id");
    }

    public function userAllowIp()
    {
        return $this->hasMany(UserAllowIp::class, 'user_id');
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

        if ($request->input("country_id")) {
            $rule->where("country_id", $request->input("country_id"));
        }

        $field_rules['mobile'] = [$rule];

        if ($request->has("name") && $request->get("type") == self::TYPE_INTERNAL)
        {
            $rule = Rule::unique($this->connection . "." . $this->getTable(), "name")->where("type", self::TYPE_INTERNAL);
            $field_rules["name"] = [$rule];
        }

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

        if ($request->get("type") == self::TYPE_INTERNAL) {
            $field_rules["name"] = ["required"];
            $field_rules["user_category_id"] = ["required"];
            $field_rules["dept_id"] = ["required"];
            $field_rules["sub_dept_id"] = ["nullable"];
            $field_rules["desg_id"] = ["required"];
        } else {
        }

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
        $this->plain_password = $plain_password;
        $this->password = bcrypt($plain_password);
    }

    public function getFieldLabels()
    {
        $labels = [
            'full_name' => "Full Name"
        ];

        $table_info = $this->getTableInfo($this->getTable());

        // d($table_info, true);

        $eception_list = [
            "code",
            "can_edit",
            "cannot_edit_reason_array",
            "can_delete",
            "cannot_delete_reason_array",
            "used_in_other_table_created_by",
            "used_in_other_table_updated_by",
            "uid",
            "plain_password",
            "password",
            "remember_token",
            "remember_token",
        ];

        foreach ($table_info['columns'] as $field => $field_attr) {

            if (!in_array($field, $eception_list)) {
                $text = str_replace(["_id", "is_"], "", $field);

                $labels[$field] = ucwords(str_function_name_to_human_text($text));
            }
        }

        // d($labels, true);

        return $labels;
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch ($field) {
            case "user_category_id":
                $ret_array = [
                    $field => $this->userCategory?->getDisplayName()
                ];
                break;
            case "dept_id":
                $ret_array = [
                    $field => $this->department?->getDisplayName()
                ];
                break;
            case "sub_dept_id":
                $ret_array = [
                    $field => $this->subDepartment?->getDisplayName()
                ];
                break;
            case "desg_id":
                $ret_array = [
                    $field => $this->designation?->getDisplayName()
                ];
                break;
            case "country_id":
                $ret_array = [
                    $field => $this->country?->getDisplayName()
                ];
                break;
        }

        // dd($ret_array);

        return $ret_array;
    }

    public static function getCodeList(String $field)
    {
        $class_info = get_class_info(static::class);

        switch ($field) {
            case "sub_type":

                $list = [];

                foreach ($class_info['constants'] as $constant => $label) {
                    if (strpos($constant, "SUB_TYPE_") !== false) {
                        $list[] = $label;
                    }
                }

                return $list;

                break;

            case "sub_type_for_signup_form":

                $list = [];

                foreach ($class_info['constants'] as $constant => $label) {
                    if (strpos($constant, "SUB_TYPE_") !== false) {
                        if (!in_array($constant, [self::SUB_TYPE_TEAM_MEMBER])) {
                            $list[] = $label;
                        }
                    }
                }

                return $list;

                break;
        }
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
        return;

        $json = $this->used_in_other_table_created_by;

        $this->used_in_other_table_created_by = $this->_updateUsageJson($json, $model->getTable(), $model->getClassDisplayName(), $counter, [
            "last_id" => $model->id
        ]);

        $this->save();
    }

    public function changeCreatedByUsageBulk(array $models)
    {
        return;

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
        return;

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
        return;

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
}
