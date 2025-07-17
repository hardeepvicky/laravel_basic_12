<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskUser extends BaseModel
{
    use HasFactory;

    protected $table = "task_link_users";

    protected static Array $unique_fields = ["task_id", "user_id"];    
}
