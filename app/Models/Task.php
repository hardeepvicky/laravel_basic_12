<?php

namespace App\Models;

use App\Exceptions\UserException;
use App\Helpers\DateUtility;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Task extends BaseModel
{
    use HasFactory;
    protected $appends = [
        'display_name',
    ];

    protected $casts = [
        'start_date_time' => 'datetime',
        'end_date_time' => 'datetime',
        'cannot_edit_reason_array' => 'array',
        'cannot_delete_reason_array' => 'array',
    ];

    protected static array $fields_containg_tags = ["description"];

    public string $field_for_create_tag = "name";

    public static array $child_model_class = [

    ];

    public const TASK_TYPE_PROJECT = "PROJECT";
    public const TASK_TYPE_TARGET = "TARGET";
    public const TASK_TYPE_OTHER = "OTHER";

    public const STATUS_DRAFT = "DRAFT";
    public const STATUS_CREATED = "CREATED";
    public const STATUS_ASSIGNED = "ASSIGNED";
    public const STATUS_CONFIRM = "CONFIRM";
    public const STATUS_IN_PROGRESS = "IN_PROGRESS";
    public const STATUS_COMPLETED = "COMPLETED";
    public const STATUS_VERIFIED = "VERIFIED";

    public const DURATION_TYPE_HOUR = "HOUR";
    public const DURATION_TYPE_DAY = "DAY";
    public const DURATION_TYPE_WEEK = "WEEK";
    public const DURATION_TYPE_MONTH = "MONTH";
    public const DURATION_TYPE_YEAR = "YEAR";

    public function beforeSave()
    {
        if ($this->is_draft) {
            $this->status = self::STATUS_DRAFT;
        }
        else if (!$this->id)
        {
            $this->status = self::STATUS_CREATED;
        }

        return parent::beforeSave();
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->convertTimeZoneForDisplay();

        $this->parseTagsForModuleLinking();
    }

    public function setDefaultAttributes() {}

    public function getUniqueValidationRules(Request $request)
    {
        return [];
    }

    public function getRequiedIfRules(Request $request)
    {
        $rules = [];

        if (!$request->input("is_draft", false)) {
            $rules["task_type"] = ["required"];
            $rules["dept_id"] = ["required"];
            $rules["assign_to_user_id"] = ["required"];
            $rules["priority"] = ["required"];
            $rules["start_date_time"] = ["required"];
            $rules["duration_type"] = ["required"];
            $rules["duration"] = ["required"];
        }

        return $rules;
    }

    public function department()
    {
        return $this->belongsTo(Department::class, "dept_id");
    }

    public function assignToUser()
    {
        return $this->belongsTo(User::class, "assign_to_user_id");
    }

    public function tags()
    {
        $class_name = str_class_name_without_namespace($this);

        return $this->hasMany(Tag::class, 'foreign_id')->where("foreign_model", $class_name);
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->name;
    }

    public function getFieldLabels()
    {
        $labels = parent::getFieldLabels();

        $labels['tag_name'] = "Tags";
        $labels['from_start_date'] = "From Start Date";
        $labels['to_start_date'] = "To Start Date";

        return $labels;
    }

    public function isAutorizeForChangeStatusToVerify(string &$error_msg)
    {
        $auth_user = Auth::user();
        if ($auth_user->hasRole(Role::TYPE_SYSTEM_ADMIN))
        {
            return true;
        }

        if ($this->created_by == $auth_user->id)
        {
            return true;
        }

        $error_msg = "Only Admin or Task creator can verify the task";

        return false;
    }

    public function canEditOrDelete(string &$error_msg)
    {
        $type_conditions = [];
        $type_conditions['type'] = Type::getType($this, "status");
        $status_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $error_msg = "Can't Edit or Delete, Task Status is " . $status_list[$this->status];

        $editable_status_label = [];

        foreach ([Task::STATUS_DRAFT, Task::STATUS_CREATED, Task::STATUS_ASSIGNED] as $status) {
            $editable_status_label[] = $status_list[$status];
        }

        $error_msg .= ", Only Status with " . implode(", ", $editable_status_label);

        $error_msg .= " can be edit or delete";

        return false;
    }

    public function changeStatus($status)
    {
        $this->status = $status;

        switch ($status) {
            case self::STATUS_ASSIGNED:
                break;

            case self::STATUS_COMPLETED:
                $this->is_completed = true;
                break;
            case self::STATUS_VERIFIED:
                $error_msg = "";
                if ($this->isAutorizeForChangeStatusToVerify($error_msg))
                {
                    $this->is_verified = true;
                }
                else
                {
                    throw new UserException($error_msg, UserException::ERROR_USER_ACTION_RESTRICT);
                }

                break;
        }

        if (!$this->save()) {
            throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
        }
    }

    public static function getEndDate($start_date_time, String $duration, String $duration_type)
    {
        $end_date_time = null;

        switch ($duration_type) {
            case self::DURATION_TYPE_HOUR:
                $end_date_time = DateUtility::change($start_date_time, $duration, DateUtility::HOURS);
                break;
            case self::DURATION_TYPE_DAY:
                $end_date_time = DateUtility::change($start_date_time, $duration, DateUtility::DAYS);
                break;
            case self::DURATION_TYPE_MONTH:
                $end_date_time = DateUtility::change($start_date_time, $duration, DateUtility::MONTHS);
                break;
            case self::DURATION_TYPE_WEEK:
                $end_date_time = DateUtility::change($start_date_time, $duration, DateUtility::WEEKS);
                break;
            case self::DURATION_TYPE_YEAR:
                $end_date_time = DateUtility::change($start_date_time, $duration, DateUtility::YEARS);
                break;
        }

        return $end_date_time;
    }

    public static function getCodeList(String $field)
    {
        $class_info = get_class_info(static::class);


        switch ($field) {
            case "task_type":

                $list = [];

                foreach ($class_info['constants'] as $constant => $label) {
                    if (strpos($constant, "TASK_TYPE_") !== false) {
                        $list[] = $label;
                    }
                }

                return $list;

                break;

            case "status":
                $list = [];

                foreach ($class_info['constants'] as $constant => $label) {
                    if (strpos($constant, "STATUS_") !== false) {
                        $list[] = $label;
                    }
                }

                return $list;
                break;

            case "duration_type":
                $list = [];

                foreach ($class_info['constants'] as $constant => $label) {
                    if (strpos($constant, "DURATION_TYPE_") !== false) {
                        $list[] = $label;
                    }
                }

                return $list;
                break;
        }
    }
}
