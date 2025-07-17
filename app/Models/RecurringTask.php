<?php

namespace App\Models;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\DateUtility;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class RecurringTask extends BaseModel
{
    use HasFactory;
    protected $appends = [
        'display_name',
    ];

    protected $casts = [
        'start_date_time' => 'datetime',
        'end_date_time' => 'datetime',
        'cannot_edit_reason_array' => 'array'
    ];

    public static array $child_model_class = [
        Task::class => [
            "foreignKey" => "recurring_task_id",
            "preventDelete" => true,
        ],
        RecurringTaskDate::class => [
            "foreignKey" => "recurring_task_id",
            "preventDelete" => false,
        ],
    ];

    protected static array $fields_containg_tags = ["description"];
    
    public string $field_for_create_tag = "name";

    public const RECURRING_DAILY = "DAILY";
    public const RECURRING_WEEKLY = "WEEKLY";
    public const RECURRING_2_WEEK = "2 WEEKLY";
    public const RECURRING_MONTHLY = "MONTHLY";
    public const RECURRING_QUATARLY = "QUATARLY";
    public const RECURRING_HALF_YEARLY = "HALF_YEARLY";
    public const RECURRING_YEARLY = "YEARLY";
    public const RECURRING_CUSTOM = "CUSTOM";

    public function afterFind()
    {
        parent::afterFind();

        $this->convertTimeZoneForDisplay();

        if ($this->recurringTaskDate?->count() > 0)
        {
            foreach($this->recurringTaskDate as $recurringTaskDate)
            {
                $recurringTaskDate->convertTimeZoneForDisplay();
            }
        }

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

        if ($request->get("is_draft")) {
        } else {
            $rules['dept_id'] = ["required"];
            $rules['assign_to_user_id'] = ["required"];
            $rules['task_type'] = ["required"];
            $rules['recurring_type'] = ["required"];
            $rules['priority'] = ["required"];
            $rules['duration'] = ["required"];
            $rules['duration_type'] = ["required"];

            if ($request->get("recurring_type") == self::RECURRING_CUSTOM) {
                $rules['recurringTaskDate'] = ["required"];
            } else {
                $rules["start_date_time"] = ["required"];
            }
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

    public function recurringTaskDate()
    {
        return $this->hasMany(RecurringTaskDate::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->name;
    }

    public static function getCodeList(String $field)
    {
        $class_info = get_class_info(static::class);

        switch ($field) {
            case "recurring_type":

                $list = [];

                foreach ($class_info['constants'] as $constant => $label) {
                    if (strpos($constant, "RECURRING_") !== false) {
                        $list[] = $label;
                    }
                }

                return $list;

                break;
        }

        return null;
    }

    

    public static function createTask(RecurringTask $recurring_task): int
    {
        $save_count = 0;

        $days = 0;

        $now = DateUtility::getDate(null, 'Y-m-d H:i:00');

        DB::beginTransaction();

        if ($recurring_task->recurring_type != self::RECURRING_CUSTOM) {
            switch ($recurring_task->recurring_type) {
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

            if ($days > 0) {
                $start_date_time = $recurring_task->start_date_time;
                $next_date = DateUtility::getDate($start_date_time, 'Y-m-d H:i:00');

                while (DateUtility::compare($next_date, $now) <= 0) {
                    try {
                        $model = new Task();

                        $record = $recurring_task->getAttributes();
                        unset($record["id"]);
                        unset($record["recurring_type"]);
                        unset($record["created_at"]);
                        unset($record["updated_at"]);
                        unset($record["created_by"]);
                        unset($record["updated_by"]);

                        $record['is_draft'] = 0;
                        $record['is_completed'] = 0;
                        $record['is_verified'] = 0;                        

                        $record['start_date_time'] = $next_date;
                        $record['recurring_task_id'] = $recurring_task->id;
                        $record["created_by"] = $record["updated_by"] = 0;

                        $record['end_date_time'] = Task::getEndDate($record['start_date_time'], $record['duration'], $record['duration_type']);

                        $count = Task::where("recurring_task_id", $record['recurring_task_id'])->where("start_date_time", $record['start_date_time'])->count();

                        if ($count == 0) {

                            $model = new Task($record);

                            if ($model->save()) {
                                $save_count++;
                            } else {
                                throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
                            }
                        }
                    } catch (Exception $ex) {
                        d($model?->custom_errors);
                        DB::rollBack();
                        throw $ex;
                    }

                    $next_date = DateUtility::change($next_date, $days, DateUtility::DAYS);
                }
            }
        } else {
            $recurring_task->load("recurringTaskDate");

            foreach ($recurring_task->recurringTaskDate as $recurringTaskDate) {
                if (DateUtility::compare($recurringTaskDate->date, $now) <= 0) {
                    try {
                        $model = new Task();
                        $record = $recurring_task->getAttributes();
                        unset($record["id"]);
                        unset($record["created_at"]);
                        unset($record["updated_at"]);
                        unset($record["created_by"]);
                        unset($record["updated_by"]);

                        $record['is_draft'] = 0;
                        $record['is_completed'] = 0;
                        $record['is_verified'] = 0;                        

                        $record['start_date_time'] = $recurringTaskDate->date->toDateTimeString();
                        $record['recurring_task_id'] = $recurring_task->id;
                        $record["created_by"] = $record["updated_by"] = 0;

                        $record['end_date_time'] = Task::getEndDate($record['start_date_time'], $record['duration'], $record['duration_type']);

                        $count = Task::where("recurring_task_id", $record['recurring_task_id'])->where("start_date_time", $record['start_date_time'])->count();

                        if ($count == 0) {
                            $model = new Task($record);

                            if ($model->save()) {
                                $recurringTaskDate->task_id = $model->id;

                                if (!$recurringTaskDate->save()) {
                                    throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
                                }

                                $save_count++;
                            } else {
                                throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
                            }
                        }
                    } catch (Exception $ex) {
                        d($model?->custom_errors);
                        DB::rollBack();
                        throw $ex;
                    }
                }
            }
        }

        DB::commit();

        return $save_count;
    }
}
