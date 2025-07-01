<?php

namespace App\Models;

class TaskRecurringDate extends BaseModel
{
    protected static Array $unique_fields = ["task_id", "date"];

    protected $casts = [
        'date' => 'date',
    ];

    public function task()  
    {
        return $this->belongsTo(Task::class);
    }

    public function getUniqueValidationRules($data)
    {
        return [];
    }
}
