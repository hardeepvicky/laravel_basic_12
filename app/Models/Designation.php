<?php

namespace App\Models;

class Designation extends BaseModel
{
    public array $child_model_class = [
        Employee::class => [
            "label" => "Employee",
            "foreignKey" => "designation_id",
            "preventDelete" => true
        ]
    ];

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
}
