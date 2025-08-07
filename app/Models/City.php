<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class City extends BaseModel
{
    protected static array $unique_fields = ["state_id", "district_id", "name"];

    protected $appends = [
        'display_name',
    ];

    public static array $child_model_class = [
        // Employee::class => [
        //     "foreignKey" => ["current_state_id", "permanent_state_id"],
        //     "preventDelete" => true
        // ]
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        $name = $this->name;

        return $name;
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")->where("district_id", $request->input("district_id"))],
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

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "state_id":
                $ret_array = [
                    $field => $this->state?->getDisplayName()
                ];
                break;

            case "district_id":
                $ret_array = [
                    $field => $this->district?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
