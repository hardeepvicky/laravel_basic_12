<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Model;

class Department extends BaseModel
{
    //
=======
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Department extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected static array $unique_fields = ["name"];

    protected static array $list_fields = ["id", "name", "short_name"];

    public static array $child_model_class = [
        // Employee::class => [
        //     "foreignKey" => "department_id",
        //     "preventDelete" => true
        // ],
        User::class => [
            "foreignKey" => "dept_id",
            "preventDelete" => true
        ],
        SubDepartment::class => [
            "foreignKey" => "department_id",
            "preventDelete" => true
        ],
        Task::class => [
            "foreignKey" => "dept_id",
            "preventDelete" => true,
        ],
        RecurringTask::class => [
            "foreignKey" => "dept_id",
            "preventDelete" => true,
        ],
    ];

    public function headUser()
    {
        return $this->belongsTo(User::class, "dept_head_user_id");
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->short_name;
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
>>>>>>> base_v3
}
