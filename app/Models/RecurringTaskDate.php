<?php

namespace App\Models;

class RecurringTaskDate extends BaseModel
{
    protected static Array $unique_fields = ["recurring_task_id", "date"];

    protected $casts = [
        'date' => 'date',
    ];

    public function task()  
    {
        return $this->belongsTo(RecurringTask::class);
    }

    public function getUniqueValidationRules($data)
    {
        return [];
    }
}
