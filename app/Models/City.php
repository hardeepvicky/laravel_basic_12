<?php

namespace App\Models;

class City extends BaseModel
{
    public array $child_model_class = [
        Employee::class => [
            "label" => "Employee",
            "foreignKey" => ["current_city_id", "permanent_city_id"],
            "preventDelete" => true
        ]
    ];


    public function state()
    {
        return $this->belongsTo(State::class);
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->name;
    }
}
