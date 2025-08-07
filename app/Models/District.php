<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class District extends BaseModel
{
    protected static array $unique_fields = ["state_id", "name"];

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

    public function city()
    {
        return $this->hasMany(City::class);
    }

    public function user()
    {
        return $this->hasMany(User::class);
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
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")->where("state_id", $request->input("state_id"))],
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
        }

        return $ret_array;
    }
}
