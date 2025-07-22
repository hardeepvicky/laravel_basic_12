<?php

namespace App\Models;

class RecurringTaskFile extends BaseModel
{
    protected static Array $unique_fields = ["recurring_task_id", "document_file_id"];
}
