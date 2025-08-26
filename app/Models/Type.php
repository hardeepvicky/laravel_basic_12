<?php

namespace App\Models;

use App\Exceptions\UserException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Type extends BaseModel
{
    protected static array $unique_fields = ["type", "name"];

    protected $appends = [
        'display_name',
    ];

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->name;
    }


    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")->where("type", $request->input("type"))],
            "code" => [Rule::unique($this->connection . "." . $this->getTable(), "code")->where("type", $request->input("type"))->where("code", "<>", "")->whereNotNull("code")],
        ];

        if ($this->id) {
            foreach ($field_rules as $field => $rules) {
                foreach ($rules as $rule) {
                    if ($rule instanceof Unique) {
                        $rule->ignore($this->id, $this->primaryKey);
                    }
                }
            }
        }

        return $field_rules;
    }

    public static function getTypes()
    {
        $types = [
            Task::class => [
                "fields" => [
                    "task_type" => "Task Type",
                    "status" => "Status",
                    "duration_type" => "Duration Type",
                    "contact_type" => "Contact Type",
                ]
            ],
            RecurringTask::class => [
                "fields" => [
                    "recurring_type" => "Recurring Type",
                ]
            ],
            Notification::class => [
                "fields" => [
                    "sub_type" => "Sub Type"
                ]
            ],
            ProductCategoryAttribute::class => [
                "fields" => [
                    "input_type" => "Input Type",
                ]
            ],
            User::class => [
                "fields" => [
                    "sub_type" => "Sub Type",
                    "sub_type_for_signup_form" => "Sub Type For Signup Form"
                ]
            ],
            Lead::class => [
                "fields" => [
                    "source_type" => "Source Type",
                    "deal_type" => "Deal Type",
                    "status_type" => "Status",
                    "payment_method_type" => "Payment Method",
                ]
            ],
            Customer::class => [
                "fields" => [
                    "deal_type" => "Deal Type",
                    "status_type" => "Status",
                ]
            ],
            LeadHistory::class => [
                "fields" => [
                    "action_type" => "Action Type",
                ]
            ],
            DispatchOrder::class => [
                "fields" => [
                    "status_type" => "Status",
                ]
            ],
            Branch::class => [
                "fields" => [
                    "type" => "Type",
                ]
            ],
        ];

        return $types;
    }

    public static function getTypeList()
    {
        $types = self::getTypes();

        foreach ($types as $model_class => $arr) {

            $model = new $model_class();

            $model_class_name = str_class_name_without_namespace($model_class);

            $model_class_display_name = $model->getClassDisplayName();

            if (isset($arr['fields']) && is_array($arr['fields']))
            {
                if (method_exists($model, "getFieldLabels"))
                {
                    $field_labels = $model->getFieldLabels();
                }
                else
                {
                    $field_labels = FieldSetting::getDynamicFields($model);
                }

                foreach ($arr['fields'] as $field => $label) {

                    if (isset($field_labels[$field]))
                    {
                        $label = $field_labels[$field];
                    }

                    $list[$model_class_name . "." . $field] = $model_class_display_name . " -> " . $label;

                }
            }
        }

        return $list;
    }

    public static function getCodes(string $find_type = "")
    {
        $types = self::getTypes();

        $data = [];

        foreach ($types as $model_class => $arr) {

            $model = new $model_class();

            $model_class_name = str_class_name_without_namespace($model_class);

            $model_class_display_name = $model->getClassDisplayName();

            if (isset($arr['fields']) && is_array($arr['fields']))
            {
                $field_labels = FieldSetting::getDynamicFields($model);

                foreach ($arr['fields'] as $field => $label) {

                    if (!method_exists($model_class, "getCodeList"))
                    {
                        throw new UserException("getCodeList() is not found in $model_class_name");
                    }

                    $code_list = $model_class::getCodeList($field);

                    if (!is_array($code_list))
                    {
                        throw new UserException("$model_class_name : getCodeList($field) return non-array result, found : " . gettype($code_list));
                    }

                    if (isset($field_labels[$field]))
                    {
                        $label = $field_labels[$field];
                    }

                    $data[$model_class_name]['label'] = $model_class_display_name;
                    $data[$model_class_name]['fields'][$field]["label"] = $label;
                    $data[$model_class_name]['fields'][$field]["code_list"] = $code_list;
                }
            }
        }

        list($find_model_class, $find_field) = explode(".", $find_type);

        if ($find_model_class && $find_field)
        {
            return $data[$find_model_class]["fields"][$find_field] ?? [];
        }
        else if ($find_model_class)
        {
            return $data[$find_model_class] ?? [];
        }

        return $data;
    }

    public static function getType($find_model_class, String $find_field)
    {
        $types = self::getTypes($find_field);

        $find_model_class = get_class($find_model_class);

        if (isset($types[$find_model_class]))
        {
            $model_type_fields = $types[$find_model_class]['fields'];
            if (isset($model_type_fields[$find_field]))
            {
                $class_name = str_class_name_without_namespace($find_model_class);
                return $class_name . "." . $find_field;
            }
        }

        throw new UserException("could not find $find_field of $find_model_class in types");
    }


    public static function getIdFromCode($type, $code)
    {
        $id_list = self::where("type", $type)->where("code", $code)->pluck("id")->toArray();

        return $id_list;
    }

    public static function getByCode($type, $code)
    {
        $records = self::where("type", $type)->where("code", $code)->get();

        return $records;
    }

    public static function getRecordsOfModel($find_model_class)
    {
        $class_name = str_class_name_without_namespace($find_model_class);

        return static::where("type", "like", $class_name . ".%")->get();
    }

    public static function getListOfModel($find_model_class, string $key_field = "code", string $value_field = "display_name") : array
    {
        $records = self::getRecordsOfModel($find_model_class);

        $list = [];

        foreach($records as $record)
        {
            if ($record->{$key_field})
            {
                $value = $record->{$value_field};
                if ($value_field == "display_name")
                {
                    $value = $record->getDisplayName();
                }

                if (strpos($record->type, ".") >= 0)
                {
                    $field = substr($record->type, strpos($record->type, ".") + 1);

                    $list[$field][$record->{$key_field}] = $value;
                }
                else
                {
                    $list[$record->{$key_field}] = $value;
                }
            }
        }

        return $list;
    }
}
