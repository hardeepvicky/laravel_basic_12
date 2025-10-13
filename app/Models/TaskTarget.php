<?php

namespace App\Models;

class TaskTarget extends BaseModel
{
    protected static Array $unique_fields = ["task_id", "target_id"];   
    
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function target()
    {
        return $this->belongsTo(Target::class);
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

            case "target_id":
                $ret_array = [
                    $field => $this->target?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
