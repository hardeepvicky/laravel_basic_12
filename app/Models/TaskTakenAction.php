<?php

namespace App\Models;

class TaskTakenAction extends BaseModel
{
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
