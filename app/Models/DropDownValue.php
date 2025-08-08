<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class DropDownValue extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected static array $unique_fields = ["type", "name"];

    protected static array $list_fields = ["id", "name", "short_name"];

    const TASK_ACTION_TAKEN = "TASK_ACTION_TAKEN";
    const TASK_RESPONSE = "TASK_RESPONSE";
    const FILE_EXTENSION = "FILE_EXTENSION";
    const COLOR = "COLOR";
    const USER_CATEGORY = "USER_CATEGORY";
    const LEAD_SOURCE = "LEAD_SOURCE";

    public static array $child_model_class = [
        // User::class => [
        //     "foreignKey" => ["sub_department_id"],
        //     "preventDelete" => true
        // ]
    ];

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        $name = $this->name;

        if ($this->short_name)
        {
            $name .= " (" . $this->short_name . ")";
        }

        return $name;
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")->where("type", $request->input("type"))],
        ];

        if ($this->id) {
            foreach ($field_rules as $field => $rules) {
                foreach ($rules as $rule) {
                    if ($rule instanceof Unique) {
                        // d([$field => $this->id]);
                        $rule->ignore($this->id, $this->primaryKey);
                    }
                }
            }
        }

        return $field_rules;
    }

    public static function getTypeList()
    {
        $list = [
            self::TASK_ACTION_TAKEN => "Task Action Taken",
            self::TASK_RESPONSE => "Task Response",
            self::FILE_EXTENSION => "File Extension",
            self::COLOR => "Colour",
            self::USER_CATEGORY => "User Category",
            self::LEAD_SOURCE => "Lead Source",
        ];

        return $list;
    }
}
