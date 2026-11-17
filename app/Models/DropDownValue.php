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

    const FILE_EXTENSION = "FILE_EXTENSION";
    const COLOR = "COLOR";
    const AUTO_SERIAL_PATTERN = "AUTO_SERIAL_PATTERN";

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
            self::FILE_EXTENSION => "File Extension",
            self::COLOR => "Colour",
            self::AUTO_SERIAL_PATTERN => "Auto Serial Pattern",
        ];

        return $list;
    }
}
