<?php

namespace App\Models;

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
                    $fields_found[] = $field;

                    if (in_array($field, $ignore_fields))
                    {

                    }
                    else
                    {
                        if (isset($last_history->model_data[$field]))
                        {
                            if ($last_history->model_data[$field] != $value)
                            {
                                $change_counters['changed']++;

                                $changes[$field] = [
                                    "type" => "changed",
                                    "label" => get_value_from_list($field_labels, $field, $field),
                                    "previous" => get_value_from_list($last_history->model_data, $field),
                                    "current" => $value
                                ];
                            }
                        }
                        else if (!is_null($value))
                        {
                            $change_counters['new']++;

                            $changes[$field] = [
                                "type" => "new",
                                "label" => get_value_from_list($field_labels, $field, $field),
                                "previous" => null,
                                "current" => $value
                            ];
                        }
                    }
                }

                foreach($last_history->model_data as $field => $value)
                {
                    if (in_array($field, $ignore_fields))
                    {
                        
                    }
                    else if (!in_array($field, $fields_found))
                    {
                        $change_counters['removed']++;                            
                        $changes[$field] = [
                            "type" => "removed",
                            "label" => get_value_from_list($field_labels, $field, $field),
                            "previous" => $value,
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
                    if (in_array($field, $ignore_fields))
                    {

                    }
                    else
                    {
                        $change_counters['new']++;

                        $changes[$field] = [
                            "type" => "new",
                            "label" => get_value_from_list($field_labels, $field, $field),
                            "previous" => null,
                            "current" => $value
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
}