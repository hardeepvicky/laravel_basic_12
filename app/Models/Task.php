<?php

namespace App\Models;

use App\Exceptions\UserException;
use App\Helpers\DateUtility;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

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

    public array $child_model_class = [
        Task::class => [
            "foreignKey" => "parent_id",
            "preventDelete" => true,
        ],
    ];

    public const TYPE_ONE_TIME = "ONE_TIME";
    public const TYPE_RECURRING = "RECURRING";

    public const ONE_TIME_IMMDIATE = "IMMDIATE";
    public const ONE_TIME_AFTER_1_DAY = "AFTER_1_DAY";
    public const ONE_TIME_AFTER_2_DAY = "AFTER_2_DAY";
    public const ONE_TIME_AFTER_3_DAY = "AFTER_3_DAY";
    public const ONE_TIME_AFTER_WEEK = "AFTER_WEEK";
    public const ONE_TIME_AFTER_MONTH = "AFTER_MONTH";
    public const ONE_TIME_CUSTOM = "CUSTOM";

    public const RECURRING_DAILY = "DAILY";
    public const RECURRING_WEEKLY = "WEEKLY";
    public const RECURRING_2_WEEK = "2 WEEKLY";
    public const RECURRING_MONTHLY = "MONTHLY";
    public const RECURRING_QUATARLY = "QUATARLY";
    public const RECURRING_YEARLY = "YEARLY";
    public const RECURRING_CUSTOM = "CUSTOM";

    public const STATUS_DRAFT = "DRAFT";
    public const STATUS_CREATED = "CREATED";
    public const STATUS_ASSIGNED = "ASSIGNED";
    public const STATUS_CONFIRM = "CONFIRM";
    public const STATUS_COMPLETED = "COMPLETED";
    public const STATUS_VERIFIED = "VERIFIED";
    public const STATUS_IN_PROGRESS = "IN_PROGRESS";

    public const COMPLETE_TIME_1_DAY = "1_DAY";
    public const COMPLETE_TIME_2_DAY = "2_DAY";
    public const COMPLETE_TIME_3_DAY = "3_DAY";
    public const COMPLETE_TIME_4_DAY = "4_DAY";
    public const COMPLETE_TIME_5_DAY = "5_DAY";
    public const COMPLETE_TIME_WEEK = "WEEK";
    public const COMPLETE_TIME_2_WEEK = "2_WEEK";
    public const COMPLETE_TIME_MONTH = "MONTH";
    public const COMPLETE_TIME_QUARTER = "QUARTER";


    public function beforeSave()
    {
        // dd($this->getAttributes());

        $group = $this->getClassDisplayName();

        if ($this->is_draft) {
            $this->status = self::STATUS_DRAFT;
        } else {            
            if ($this->type == self::TYPE_ONE_TIME) {
                if (!$this->one_time_type) {
                    $this->addError("One Time", "Can not be empty", $group);
                }

                if (!$this->start_date_time) {
                    $this->addError("Start Date", "Can not be empty", $group);
                }

                if (!$this->end_date_time) {
                    $this->addError("End Date", "Can not be empty", $group);
                }
            }
        }

        return parent::beforeSave();
    }

    public function setDefaultAttributes() {}

    public function getFieldLabels()
    {
        $labels = parent::getFieldLabels();

        $labels['name'] = "Title";
        $labels['one_time_type'] = "When To Start";
        $labels['recurring_type'] = "Recurring";
        $labels['start_date_time'] = "Start Date";
        $labels['end_date_time'] = "End Date";
        $labels['complete_time_in_days'] = "Task Should complete In Days";

        return $labels;
    }

    public function getUniqueValidationRules(Request $request)
    {
        return [];
    }

    public function getRequiedIfRules(Request $request)
    {
        $rules = [];

        if (!$request->input("is_draft", false))
        {
            $rules["priority"] = ["required"];
        }

        if ($request->input("type") == self::TYPE_ONE_TIME)
        {
            $rules["assign_to_user_id"] = ["required"];
            $rules["one_time_type"] = ["required"];
            $rules["start_date_time"] = ["required"];
            $rules["end_date_time"] = ["required"];
        }
        else
        {
            $rules["recurring_type"] = ["required"];
            $rules["complete_time_in_days"] = ["required"];
        }

        return $rules;
    }

    public function assignToUser()
    {
        return $this->belongsTo(User::class, "assign_to_user_id");
    }

    public function taskRecurringDate()
    {
        return $this->hasMany(TaskRecurringDate::class);
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

        $this->save();

        switch($status)
        {
            case self::STATUS_ASSIGNED:
                break;
        }
    }

    public static function getCodeList(String $field)
    {
        switch ($field) {
            case "type":
                return [
                    self::TYPE_ONE_TIME,
                    self::TYPE_RECURRING,
                ];
                break;
            case "one_time_type":
                return [
                    self::ONE_TIME_AFTER_1_DAY,
                    self::ONE_TIME_AFTER_2_DAY,
                    self::ONE_TIME_AFTER_3_DAY,
                    self::ONE_TIME_AFTER_MONTH,
                    self::ONE_TIME_AFTER_WEEK,
                    self::ONE_TIME_CUSTOM
                ];
                break;

            case "recurring_type":
                return [
                    self::RECURRING_DAILY,
                    self::RECURRING_WEEKLY,
                    self::RECURRING_2_WEEK,
                    self::RECURRING_MONTHLY,
                    self::RECURRING_QUATARLY,
                    self::RECURRING_YEARLY,
                    self::RECURRING_CUSTOM,
                ];
                break;

            case "status":
                return [
                    self::STATUS_ASSIGNED,
                    self::STATUS_COMPLETED,
                    self::STATUS_CONFIRM,
                    self::STATUS_DRAFT,
                    self::STATUS_VERIFIED,
                    self::STATUS_IN_PROGRESS,
                    self::STATUS_CREATED,
                ];
                break;

            case "complete_time_in_days":
                return [
                    self::COMPLETE_TIME_1_DAY,
                    self::COMPLETE_TIME_2_DAY,
                    self::COMPLETE_TIME_3_DAY,
                    self::COMPLETE_TIME_4_DAY,
                    self::COMPLETE_TIME_5_DAY,
                    self::COMPLETE_TIME_WEEK,
                    self::COMPLETE_TIME_2_WEEK,
                    self::COMPLETE_TIME_MONTH,
                    self::COMPLETE_TIME_QUARTER,
                ];
                break;
        }
    }

    public static function setDates(array $record) : array
    {
        $days_to_add = 0;

        switch($record['complete_time_in_days'])
        {
            case self::COMPLETE_TIME_1_DAY:
                $days_to_add = 1;
                break;
            case self::COMPLETE_TIME_2_DAY:
                $days_to_add = 2;
                break;
            case self::COMPLETE_TIME_3_DAY:
                $days_to_add = 3;
                break;
            case self::COMPLETE_TIME_4_DAY:
                $days_to_add = 4;
                break;
            case self::COMPLETE_TIME_5_DAY:
                $days_to_add = 5;
                break;
            case self::COMPLETE_TIME_WEEK:
                $days_to_add = 7;
                break;
            case self::COMPLETE_TIME_2_WEEK:
                $days_to_add = 14;
                break;
            case self::COMPLETE_TIME_MONTH:
                $days_to_add = 30;
                break;
            case self::COMPLETE_TIME_QUARTER:
                $days_to_add = 90;
                break;
        }

        if (isset($record['start_date_time']))
        {
            $record['end_date_time'] = DateUtility::change($record['start_date_time'], $days_to_add, DateUtility::DAYS, DateUtility::DATE_FORMAT);
        }

        return $record;
    }

    public static function createRecurringTask(Task $task) : int
    {
        $save_count = 0;

        if ($task->type == self::TYPE_RECURRING)
        {
            $today = date(DateUtility::DATE_FORMAT);

            $days = 0;

            switch($task->recurring_type)
            {
                case self::RECURRING_DAILY:
                    $days = 1;
                    break;
                case self::RECURRING_WEEKLY:
                    $days = 7;
                    break;
                case self::RECURRING_2_WEEK:
                    $days = 14;
                    break;
                case self::RECURRING_MONTHLY:
                    $days = 30;
                    break;
                case self::RECURRING_QUATARLY:
                    $days = 90;
                    break;
                case self::RECURRING_YEARLY:
                    $days = 365;
                    break;                
            }

            if ($days > 0)
            {
                $next_date = DateUtility::getDate($task->created_at, DateUtility::DATE_FORMAT);

                while(DateUtility::compare($next_date, $today) <= 0)
                {
                    try
                    {
                        $record = $task->getAttributes();
                        unset($record["id"]);
                        unset($record["created_at"]);
                        unset($record["updated_at"]);
                        unset($record["created_by"]);
                        unset($record["updated_by"]);

                        
                        $record['type'] = self::TYPE_ONE_TIME;
                        $record['one_time_type'] = self::ONE_TIME_CUSTOM;
                        $record['is_draft'] = 0;
                        $record['is_completed'] = 0;
                        $record['is_verified'] = 0;
                        $record['can_edit'] = 0;
                        $record['cannot_edit_reason_array'] = ['Task created from recurring task by system'];

                        $record['start_date_time'] = $next_date;
                        $record['parent_id'] = $task->id;
                        $record["created_by"] = $record["updated_by"] = 0;

                        $record = self::setDates($record);
                        
                        // d($record);

                        $count = Task::where("parent_id", $record['parent_id'])->whereDate("start_date_time", $record['start_date_time'])->count();

                        if ($count == 0)
                        {
                            $model = new Task($record);
                            
                            if ($model->save())
                            {
                                $save_count++;
                            }
                            else
                            {
                                throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
                            }
                        }
                    }
                    catch(Exception $ex)
                    {
                        d($model->custom_errors);
                        throw $ex;
                    }

                    $next_date = DateUtility::change($next_date, $days, DateUtility::DAYS, DateUtility::DATE_FORMAT);
                }
            }
        }

        return $save_count;
    }
}
