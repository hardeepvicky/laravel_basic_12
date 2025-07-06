<?php

namespace App\Models;

use App\Exceptions\UserException;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

class Task extends BaseModel
{
    use HasFactory;
    protected $appends = [
        'display_name',
    ];

    protected $casts = [
        'start_date_time' => 'date',
        'end_date_time' => 'date',
        'cannot_edit_reason_array' => 'array'
    ];

    protected static array $fields_containg_tags = ["description"];

    public static array $child_model_class = [
        Task::class => [
            "foreignKey" => "parent_id",
            "preventDelete" => true,
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

    public function beforeSave()
    {
        if ($this->is_draft) {
            $this->status = self::STATUS_DRAFT;
        }

        return parent::beforeSave();
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
            $rules["priority"] = ["required"];
            $rules["assign_to_user_id"] = ["required"];
            $rules["start_date_time"] = ["required"];
            $rules["end_date_time"] = ["required"];
        }

        return $rules;
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
                $this->is_verified = true;
                break;
        }

        if (!$this->save()) {
            throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
        }
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
        }
    }
}
