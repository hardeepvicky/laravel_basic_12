<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class AppMenuRelatedLink extends BaseModel
{
    protected static Array $unique_fields = ["app_menu_id", "name"];

    protected $casts = [
        "route_name_array" => "array"
    ];

    public function appMenu()  
    {
        return $this->belongsTo(AppMenu::class);
    }

    public function getUniqueValidationRules($data)
    {
        $field_rules = [
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")->where("app_menu_id", $data['app_menu_id'])],
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
}
