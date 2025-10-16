<?php

namespace App\Models;

class TaskTodo extends BaseModel
{
    protected static Array $unique_fields = ["task_id"];   
    
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "task_id":
                $ret_array = [
                    $field => $this->task?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
