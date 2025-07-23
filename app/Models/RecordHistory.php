<?php

namespace App\Models;

use App\Helpers\DateUtility;
use Carbon\Carbon;
use DateTime;

class RecordHistory extends BaseMongoModel
{
    protected static array $unique_fields = ["model_class", "model_id"];

    const ACTION_INSERT = "INSERT";
    const ACTION_UPDATE = "UPDATE";
    const ACTION_DELETE = "DELETE";

    public static function getActionList()
    {
        $list = [
            self::ACTION_INSERT => "Insert",
            self::ACTION_UPDATE => "Update",
            self::ACTION_DELETE => "Delete",
        ];

        return $list;
    }

    public function getFieldLabels()
    {
        $field_labels = [
            "model_class" => "Module",
            "model_id" => "Module ID",
            "action" => "Action",
            "model_data" => "model_data",
            "changes" => "Changes",
            "change_counters" => "Change Counters",
            "created_at" => "Changed at",
            "created_by" => "Changed By",
        ];

        return $field_labels;
    }

    public static function createHistory(BaseModelInterface $model, array $model_data, bool $is_deleted = false)
    {
        $ignore_models = [
            Tag::class,
            TaskFile::class,
            TaskUser::class,
            RecurringTaskDate::class,
            RecurringTaskFile::class,
            RecurringTaskUser::class
        ];

        $model_class = get_class($model);

        if (in_array($model_class, $ignore_models))
        {
            return;
        }

        $conditions = [
            "model_class" => str_class_name_without_namespace($model),
            "model_id" => $model->id
        ];

        $save_model = null;

        $ignore_fields = [
            "created_at", "updated_at", 
            "created_by", "updated_by"
        ];

        $field_labels = FieldSetting::getDynamicFields($model);

        if ($is_deleted)
        {
            $save_data = $conditions;
            $save_data['action'] = SELF::ACTION_DELETE;
            $save_data['model_data'] = $model_data;

            $save_model = self::create($save_data);
        }
        else
        {
            $last_history = self::where("model_class", $conditions['model_class'])->where("model_id", $conditions['model_id'])->orderBy('id', 'DESC')->first();

            if ($last_history && $last_history->model_data)
            {
                $change_counters = [
                    "new" => 0,
                    "changed" => 0,
                    "removed" => 0
                ];

                $changes = [];

                $fields_found = [];

                foreach($model_data as $field => $value)
                {
                    $parse_value = self::getParseValue($value);

                    $fields_found[] = $field;

                    if (in_array($field, $ignore_fields))
                    {

                    }
                    else if (!is_null($parse_value))
                    {
                        if (isset($last_history->model_data[$field]))
                        {
                            if ($last_history->model_data[$field] != $value)
                            {
                                $change_counters['changed']++;

                                $changes[$field] = [
                                    "type" => "changed",
                                    "label" => get_value_from_list($field_labels, $field, $field),
                                    "previous" => self::getParseValue(get_value_from_list($last_history->model_data, $field)),
                                    "current" => $parse_value
                                ];
                            }
                        }
                        else
                        {
                            $change_counters['new']++;

                            $changes[$field] = [
                                "type" => "new",
                                "label" => get_value_from_list($field_labels, $field, $field),
                                "previous" => null,
                                "current" => $parse_value
                            ];
                        }
                    }
                }

                foreach($last_history->model_data as $field => $value)
                {
                    $parse_value = self::getParseValue($value);

                    if (in_array($field, $ignore_fields))
                    {
                        
                    }
                    else if (!in_array($field, $fields_found) && !is_null($parse_value))
                    {
                        $change_counters['removed']++;                            
                        $changes[$field] = [
                            "type" => "removed",
                            "label" => get_value_from_list($field_labels, $field, $field),
                            "previous" => $parse_value,
                            "current" => null
                        ];
                    }
                }

                $save_data = $conditions;
                $save_data['action'] = SELF::ACTION_UPDATE;
                $save_data['model_data'] = $model_data;
                $save_data['changes'] = $changes;
                $save_data['change_counters'] = $change_counters;

                $save_model = self::create($save_data);
            }
            else
            {
                $change_counters = [
                    "new" => 0,
                ];

                $changes = [];
                
                foreach($model_data as $field => $value)
                {
                    $parse_value = self::getParseValue($value);

                    if (in_array($field, $ignore_fields))
                    {

                    }
                    else if (!is_null($parse_value))
                    {
                        $change_counters['new']++;

                        $changes[$field] = [
                            "type" => "new",
                            "label" => get_value_from_list($field_labels, $field, $field),
                            "previous" => null,
                            "current" => $parse_value
                        ];
                    }
                }
                
                $save_data = $conditions;
                $save_data['action'] = SELF::ACTION_INSERT;
                $save_data['model_data'] = $model_data;
                $save_data['changes'] = $changes;
                $save_data['change_counters'] = $change_counters;

                $save_model = self::create($save_data);
            }
        }
        
        return $save_model;
    }

    private static function getParseValue($value)
    {
        $parse_value = $value;

        if (is_object($value))
        {
            $parse_value = (array) $value;
        }
        else if (is_array($value))
        {
            $parse_value = implode(",", $value);
        }
        else if (is_bool($value))
        {
            $parse_value = $value ? "Yes" : "No";
        }
        else
        {
            $format = "Y-m-d";
            $sub_value = $parse_value;

            if (strpos($parse_value, "T") !== false)
            {
                $sub_value = substr($parse_value, 0, strpos($parse_value, "T"));
            }

            if (strpos($parse_value, " ")  !== false)
            {
                $sub_value = substr($parse_value, 0, strpos($parse_value, " "));
            }

            $dateTime = \DateTime::createFromFormat($format, $sub_value);

            if ($dateTime && $dateTime->format($format) === $sub_value)
            {
                $parse_value = DateUtility::getDate($parse_value, DateUtility::DATETIME_OUT_FORMAT);
            }
        }

        return $parse_value;
    }
}