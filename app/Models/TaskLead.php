<?php

namespace App\Models;

class TaskLead extends BaseModel
{
    protected static Array $unique_fields = ["task_id", "lead_id"];   
    
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
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

            case "lead_id":
                $ret_array = [
                    $field => $this->lead?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
