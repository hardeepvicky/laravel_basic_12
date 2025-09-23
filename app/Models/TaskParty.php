<?php

namespace App\Models;

class TaskParty extends BaseModel
{
    protected static Array $unique_fields = ["task_id", "party_id"];   
    
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
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

            case "party_id":
                $ret_array = [
                    $field => $this->party?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
