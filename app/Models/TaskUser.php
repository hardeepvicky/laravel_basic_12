<?php

namespace App\Models;

class TaskUser extends BaseModel
{
    protected $table = "task_link_users";

    protected static Array $unique_fields = ["task_id", "user_id"];    
}
