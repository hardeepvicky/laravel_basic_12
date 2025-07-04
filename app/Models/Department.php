<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Department extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    public static array $child_model_class = [
        // Employee::class => [
        //     "foreignKey" => "department_id",
        //     "preventDelete" => true
        // ],
        // User::class => [
        //     "foreignKey" => "department_id",
        //     "preventDelete" => true
        // ],
        SubDepartment::class => [
            "foreignKey" => "department_id",
            "preventDelete" => true
        ],
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
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")],
            "level" => [Rule::unique($this->connection . "." . $this->getTable(), "level")],
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
}
