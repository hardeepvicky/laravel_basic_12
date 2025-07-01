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

    public static function getTypes()
    {
        $types = [
            "task" => [
                "label" => "Task",
                "fields" => [
                    "type" => "Type",
                    "one_time_type" => "One Time",
                    "recurring_type" => "Recurring",
                    "status" => "Status",
                    "complete_time_in_days" => "Complete In Days",
                ]
            ]
        ];

        return $types;
    }

    public static function getTypeList()
    {
        $types = self::getTypes();

        $list = [];

        foreach ($types as $group => $arr) {
            foreach ($arr['fields'] as $field => $label) {
                $list[$group . "." . $field] = $arr['label'] . "->" . $label;
            }
        }

        return $list;
    }

    public static function getType(BaseModelInterface $model_class, $find_field)
    {
        $class_name = $model_class->getClassDisplayName();

        $types = self::getTypes();

        foreach ($types as $group => $arr) {
            if (strtolower($group) == strtolower($class_name)) {

                if (isset($arr["fields"][$find_field])) {
                    return $class_name . "." . $find_field;
                }
            }
        }

        throw new UserException("could not find $find_field of $class_name in types");
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")->where("type", $request->input("type"))],
            "code" => [Rule::unique($this->connection . "." . $this->getTable(), "code")->where("type", $request->input("type"))],
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

    public static function getIdFromCode($type, $code)
    {
        $id_list = self::where("type", $type)->where("code", $code)->pluck("id")->toArray();

        return $id_list;
    }
}
