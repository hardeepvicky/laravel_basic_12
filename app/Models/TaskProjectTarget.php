<?php

namespace App\Models;

class TaskProjectTarget extends BaseModel
{
    protected static Array $unique_fields = ["task_id", "project_target_id"];   
    
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function projectTarget()
    {
        return $this->belongsTo(ProjectTarget::class);
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

            case "project_target_id":
                $ret_array = [
                    $field => $this->projectTarget?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
