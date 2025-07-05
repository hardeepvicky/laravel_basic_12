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
        'start_date_time' => 'date',
        'end_date_time' => 'date',
        'cannot_edit_reason_array' => 'array'
    ];

    public static array $child_model_class = [
        // Task::class => [
        //     "foreignKey" => "recurring_task_id",
        //     "preventDelete" => true,
        // ],
        RecurringTaskDate::class => [
            "foreignKey" => "recurring_task_id",
            "preventDelete" => false,
        ],
    ];

    public const RECURRING_DAILY = "DAILY";
    public const RECURRING_WEEKLY = "WEEKLY";
    public const RECURRING_2_WEEK = "2 WEEKLY";
    public const RECURRING_MONTHLY = "MONTHLY";
    public const RECURRING_QUATARLY = "QUATARLY";
    public const RECURRING_HALF_YEARLY = "HALF_YEARLY";
    public const RECURRING_YEARLY = "YEARLY";
    public const RECURRING_CUSTOM = "CUSTOM";

    public const COMPLETE_TIME_1_DAY = "1_DAY";
    public const COMPLETE_TIME_2_DAY = "2_DAY";
    public const COMPLETE_TIME_3_DAY = "3_DAY";
    public const COMPLETE_TIME_4_DAY = "4_DAY";
    public const COMPLETE_TIME_5_DAY = "5_DAY";
    public const COMPLETE_TIME_WEEK = "WEEK";
    public const COMPLETE_TIME_2_WEEK = "2_WEEK";
    public const COMPLETE_TIME_MONTH = "MONTH";
    public const COMPLETE_TIME_IN_3_MONTH = "IN_3_MONTH";
    public const COMPLETE_TIME_IN_6_MONTH = "IN_6_MONTH";
    public const COMPLETE_TIME_IN_9_MONTH = "IN_9_MONTH";
    public const COMPLETE_TIME_IN_YEAR = "YEAR";
    public const COMPLETE_TIME_IN_2_YEAR = "IN_2_YEAR";

    public function setDefaultAttributes() {}

    public function getUniqueValidationRules(Request $request)
    {
        return [];
    }

    public function getRequiedIfRules(Request $request)
    {
        $rules = [];

        if ($request->get("is_draft"))
        {

        }
        else
        {
            $rules['assign_to_user_id'] = ["required"];
            $rules['recurring_type'] = ["required"];
            $rules['priority'] = ["required"];
            $rules['task_should_be_complete_in'] = ["required"];

            if ($request->get("recurring_type") == self::RECURRING_CUSTOM)
            {
                $rules['recurringTaskDate'] = ["required"];
            }
            else
            {
                $rules["start_date_time"] = ["required"];
            }
        }

        return $rules;
    }

    public function assignToUser()
    {
        return $this->belongsTo(User::class, "assign_to_user_id");
    }

    public function recurringTaskDate()
    {
        return $this->hasMany(RecurringTaskDate::class);
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

                foreach($class_info['constants'] as $constant => $label)
                {
                    if (strpos($constant, "RECURRING_") !== false)
                    {
                        $list[] = $label;
                    }
                }

                return $list;

                break;
             case "task_should_be_complete_in":
                $list = [];

                foreach($class_info['constants'] as $constant => $label)
                {
                    if (strpos($constant, "COMPLETE_TIME_") !== false)
                    {
                        $list[] = $label;
                    }
                }

                return $list;
                break;
        }

        return null;
    }

    public static function setDates(array $record): array
    {
        if (isset($record['start_date_time']))
        {
            $start_date = $record['start_date_time'];
            $end_date = null;

            switch ($record['task_should_be_complete_in']) {
                case self::COMPLETE_TIME_1_DAY:
                    $end_date = DateUtility::change($start_date, 1, DateUtility::DAYS, DateUtility::DATE_FORMAT);
                    break;
                case self::COMPLETE_TIME_2_DAY:
                    $end_date = DateUtility::change($start_date, 2, DateUtility::DAYS, DateUtility::DATE_FORMAT);
                    break;
                case self::COMPLETE_TIME_3_DAY:
                    $end_date = DateUtility::change($start_date, 3, DateUtility::DAYS, DateUtility::DATE_FORMAT);
                    break;
                case self::COMPLETE_TIME_4_DAY:
                    $end_date = DateUtility::change($start_date, 4, DateUtility::DAYS, DateUtility::DATE_FORMAT);
                    break;
                case self::COMPLETE_TIME_5_DAY:
                    $end_date = DateUtility::change($start_date, 5, DateUtility::DAYS, DateUtility::DATE_FORMAT);
                    break;
                case self::COMPLETE_TIME_WEEK:
                    $end_date = DateUtility::change($start_date, 7, DateUtility::DAYS, DateUtility::DATE_FORMAT);
                    break;
                case self::COMPLETE_TIME_2_WEEK:
                    $end_date = DateUtility::change($start_date, 14, DateUtility::DAYS, DateUtility::DATE_FORMAT);
                    break;
                case self::COMPLETE_TIME_MONTH:
                    $end_date = DateUtility::change($start_date, 1, DateUtility::MONTHS, DateUtility::DATE_FORMAT);
                    break;
                case self::COMPLETE_TIME_IN_3_MONTH:
                    $end_date = DateUtility::change($start_date, 3, DateUtility::MONTHS, DateUtility::DATE_FORMAT);
                    break;
                case self::COMPLETE_TIME_IN_6_MONTH:
                    $end_date = DateUtility::change($start_date, 6, DateUtility::MONTHS, DateUtility::DATE_FORMAT);
                    break;
                case self::COMPLETE_TIME_IN_9_MONTH:
                    $end_date = DateUtility::change($start_date, 9, DateUtility::MONTHS, DateUtility::DATE_FORMAT);
                    break;
                case self::COMPLETE_TIME_IN_YEAR:
                    $end_date = DateUtility::change($start_date, 1, DateUtility::YEARS, DateUtility::DATE_FORMAT);
                    break;
                case self::COMPLETE_TIME_IN_2_YEAR:
                    $end_date = DateUtility::change($start_date, 2, DateUtility::YEARS, DateUtility::DATE_FORMAT);
                    break;
            }

            $record['end_date_time'] = $end_date;
        }

        return $record;
    }

    public static function createTask(RecurringTask $recurring_task): int
    {
        $save_count = 0;

        $days = 0;

        $today = date(DateUtility::DATE_FORMAT);

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
                $start_date = $recurring_task->start_date_time;
                $next_date = DateUtility::getDate($start_date, DateUtility::DATE_FORMAT);

                while (DateUtility::compare($next_date, $today) <= 0) {
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
                        $record['can_edit'] = 0;
                        $record['cannot_edit_reason_array'] = ['Task created from recurring task by system'];

                        $record['start_date_time'] = $next_date;
                        $record['recurring_task_id'] = $recurring_task->id;
                        $record["created_by"] = $record["updated_by"] = 0;

                        $record = self::setDates($record);

                        // d($record, true);

                        $count = Task::where("recurring_task_id", $record['recurring_task_id'])->whereDate("start_date_time", $record['start_date_time'])->count();

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

                    $next_date = DateUtility::change($next_date, $days, DateUtility::DAYS, DateUtility::DATE_FORMAT);
                }
            }
        } else {
            $recurring_task->load("recurringTaskDate");

            foreach ($recurring_task->recurringTaskDate as $task_custom_date) {
                if (DateUtility::compare($task_custom_date->date, $today) <= 0) {
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
                        $record['can_edit'] = 0;
                        $record['cannot_edit_reason_array'] = ['Task created from recurring task by system'];

                        $record['start_date_time'] = $task_custom_date->date->toDateString();
                        $record['recurring_task_id'] = $recurring_task->id;
                        $record["created_by"] = $record["updated_by"] = 0;

                        $record = self::setDates($record);

                        // d($record, true);

                        $count = Task::where("recurring_task_id", $record['recurring_task_id'])->whereDate("start_date_time", $record['start_date_time'])->count();

                        if ($count == 0) {
                            $model = new Task($record);

                            if ($model->save()) {
                                $task_custom_date->task_id = $model->id;

                                if (!$task_custom_date->save())
                                {
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
