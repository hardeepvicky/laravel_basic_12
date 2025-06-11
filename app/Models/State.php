<?php

namespace App\Models;

class State extends BaseModel
{
    public array $child_model_class = [
        Employee::class => [
            "label" => "Employee",
            "foreignKey" => ["current_state_id", "permanent_state_id"],
            "preventDelete" => true
        ]
    ];

    public function city()
    {
        return $this->hasMany(City::class);
    }

    public function user()
    {
        return $this->hasMany(User::class);
    }
}
