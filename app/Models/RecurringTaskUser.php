<?php

namespace App\Models;

class RecurringTaskUser extends BaseModel
{
    protected static Array $unique_fields = ["recurring_task_id", "user_id"];    
}
