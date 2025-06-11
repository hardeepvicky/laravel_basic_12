<?php

namespace App\Models;

class Employee extends BaseModel
{
    protected $dates = ["dob", "doj"];

    protected $casts = [
        'dob' => 'date',
        'doj' => 'date',
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

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function currentState()
    {
        return $this->belongsTo(State::class, "current_state_id");
    }

    public function permanentState()
    {
        return $this->belongsTo(State::class, "permanent_state_id");
    }

    public function currentCity()
    {
        return $this->belongsTo(City::class, "current_city_id");
    }

    public function permanentCity()
    {
        return $this->belongsTo(City::class, "permanent_city_id");
    }
}
