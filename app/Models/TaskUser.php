<?php

namespace App\Models;

class TaskUser extends BaseModel
{
    protected $table = "task_link_users";

    protected static Array $unique_fields = ["task_id", "user_id"];   
    
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "task_id":
                $ret_array = [
                    $field => $this->task->getDisplayName()
                ];
                break;

            case "user_id":
                $ret_array = [
                    $field => $this->user->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
