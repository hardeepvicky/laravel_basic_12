<?php

namespace App\Models;

class TaskDealer extends BaseModel
{
    protected static Array $unique_fields = ["task_id", "dealer_id"];   
    
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
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

            case "dealer_id":
                $ret_array = [
                    $field => $this->dealer?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
