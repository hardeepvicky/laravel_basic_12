<?php

namespace App\Models;

use App\Exceptions\UserException;
use App\Helpers\DateUtility;
use App\Helpers\LaravelExtend;
use App\Notifications\TaskStatusChange;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        TaskFile::class => [
            "foreignKey" => "task_id",
            "preventDelete" => false
        ],
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

    public const CONTACT_TYPE_LEAD = "LEAD";
    public const CONTACT_TYPE_CUSTOMER = "CUSTOMER";
    public const CONTACT_TYPE_DEALER = "DEALER";
    public const CONTACT_TYPE_PARTY = "PARTY";

    public function beforeSave()
    {
        if ($this->is_draft) {
            $this->status = self::STATUS_DRAFT;
        } else if (!$this->id && !$this->status) {
            $this->status = self::STATUS_CREATED;
        }

        return parent::beforeSave();
    }

    public function afterFindForCsv()
    {
        parent::afterFind();
    }

    public function assignToUser()
    {
        return $this->belongsTo(User::class, "assign_to_user_id");
    }

    public function documentFile()
    {
        return $this->belongsToMany(DocumentFile::class, TaskFile::class);
    }

    public function taskFile()
    {
        return $this->hasMany(TaskFile::class, 'task_id');
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->name;
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
            $rules["assign_to_user_id"] = ["required"];
            $rules["priority"] = ["required"];
            $rules["start_date_time"] = ["required"];
            $rules["duration_type"] = ["required"];
            $rules["duration"] = ["required"];
        }

        return $rules;
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

    public function canDelete(string &$error_msg)
    {
        $status_codes = [Task::STATUS_DRAFT, Task::STATUS_CREATED, Task::STATUS_ASSIGNED, Task::STATUS_CONFIRM];

        if (in_array($this->status, $status_codes) )
        {
            return true;
        }

        $type_conditions = [];
        $type_conditions['type'] = Type::getType($this, "status");
        $status_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");
        
        $error_msg = "Can't Delete, Task Status is " . $status_list[$this->status];

        $editable_status_label = [];

        foreach($status_list as $status_code => $label)
        {
            if (!in_array($status_code, $status_codes))
            {
                $editable_status_label[] = $label;
            }
        }
        
        $error_msg .= ", Only Status with " . implode(", ", $editable_status_label);

        $error_msg .= " can be delete";

        return false;
    }

    public function changeStatus($status)
    {
        if ($this->status == $status) {
            return false;
        }

        try {
            $this->load(["assignToUser"]);

            DB::beginTransaction();

            $old_status = $this->status;

            $admin_user_list = LaravelExtend::getAdminUserList();

            if ($status == self::STATUS_COMPLETED)
            {
                $user_id_list = array_keys($admin_user_list);
                $user_id_list[] = $this->assignToUser->id;
                $user_id_list = array_merge($user_id_list, $this->user->pluck("id")->toArray());

                $user_name_list = [];
                $user_name_list[] = $this->assignToUser->getDisplayName();
                foreach($this->user as $user)
                {
                    $user_name_list[] = $user->getDisplayName();
                }

                if (!in_array(LaravelExtend::getAuthUserId(), $user_id_list))
                {
                    $msg = "Only " . implode(", ", $user_name_list);
                    $msg .= " can mark complete for task " . $this->name;

                    throw new UserException($msg, UserException::ERROR_USER_ACTION_RESTRICT);
                }
            }

            if ($status == self::STATUS_VERIFIED)
            {
                $user_id_list = $this->assignToUser->designation->getUserIdListBasedOnDesignationLevel();
                $user_id_list = array_merge($user_id_list, array_keys($admin_user_list));
                $user_id_list[] = $this->created_by;

                $users = User::find($user_id_list);
                $user_name_list = [];
                foreach($users as $user)
                {
                    $user_name_list[] = $user->getDisplayName();
                }

                if (!in_array(LaravelExtend::getAuthUserId(), $user_id_list))
                {
                    $msg = "Only " . implode(", ", $user_name_list);
                    $msg .= " can verify the task " . $this->name;

                    throw new UserException($msg, UserException::ERROR_USER_ACTION_RESTRICT);
                }
            }

            switch ($status) {
                case self::STATUS_ASSIGNED:
                    break;

                case self::STATUS_COMPLETED:
                    $this->is_completed = true;
                    $this->complete_date_time = DateUtility::getDate(null, DateUtility::DATETIME_FORMAT);
                    $this->complete_by = LaravelExtend::getAuthUserId();
                    break;

                case self::STATUS_VERIFIED:
                    $this->is_verified = true;
                    $this->verified_date_time = DateUtility::getDate(null, DateUtility::DATETIME_FORMAT);
                    $this->verified_by = LaravelExtend::getAuthUserId();
                    break;
            }

            $this->status = $status;

            if (!$this->saveQuietly()) {
                throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
            }

            $type = Type::getType($this, "status");

            if ($old_status) {
                $old_status_type = Type::getByCode($type, $old_status);

                if ($old_status_type->count() == 0) {
                    throw new UserException("Type $type not found : " . $old_status);
                } else if ($old_status_type->count() > 1) {
                    throw new UserException("Type $type more than 1 found : " . $old_status);
                }

                $old_status_type = $old_status_type->first();
            }

            $new_status_type = Type::getByCode($type, $status);

            if ($new_status_type->count() == 0) {
                throw new UserException("Type $type not found : " . $status);
            } else if ($new_status_type->count() > 1) {
                throw new UserException("Type $type more than 1 found : " . $status);
            }

            $new_status_type = $new_status_type->first();

            $notification_data = [
                'from_user_id' => LaravelExtend::getAuthUserId(),
                "sub_type" => Notification::SUB_TYPE_INFO,
                "model_name" => get_class($this),
                'model_id' => $this->id,
                "link" => route("task.index", ["id" => $this->id]),
                "msg" => ""
            ];

            if ($this->prev_status == Task::STATUS_CREATED) {
                if ($this->task->status == Task::STATUS_ASSIGNED) {
                    $notification_data['msg'] = "New Task assign to " . $this->assignToUser?->getDisplayName();
                }
            } else if ($this->status == Task::STATUS_VERIFIED) {
                $msg = "Task $this->name has verified";

                $notification_data['msg'] = $msg;
            } else {
                $msg = "Status of Task $this->name change from ";

                $msg .= $old_status_type?->name ?? "";

                $msg .= " to ";

                $msg .= $new_status_type?->name ?? "";

                $notification_data['msg'] = $msg;
            }

            $notificationObj = new TaskStatusChange($this, $notification_data);

            if ($this->assign_to_user_id == LaravelExtend::getAuthUserId()) {
                $created_by_user = User::find($this->created_by);
                if ($created_by_user)
                {
                    $created_by_user->notify($notificationObj);
                }
            } else {
                $this->assignToUser->notify($notificationObj);
            }

            if ($this->is_send_notification && $this->user->count() > 0)
            {
                \Illuminate\Support\Facades\Notification::send($this->user, $notificationObj);   
            }

            DB::commit();

        } catch (Exception $ex) {

            DB::rollBack();

            throw $ex;
        }

        return true;
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

            case "contact_type":
                $list = [];

                foreach ($class_info['constants'] as $constant => $label) {
                    if (strpos($constant, "CONTACT_TYPE_") !== false) {
                        $list[] = $label;
                    }
                }

                return $list;
                break;
        }
    }
}
