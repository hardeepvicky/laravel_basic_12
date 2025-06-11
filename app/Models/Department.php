<?php

namespace App\Models;

class Department extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    public array $child_model_class = [
        Employee::class => [
            "label" => "Employee",
            "foreignKey" => "department_id",
            "preventDelete" => true
        ]
    ];

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->name;
    }
}
