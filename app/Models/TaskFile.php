<?php

namespace App\Models;

class TaskFile extends BaseModel
{
    protected static Array $unique_fields = ["task_id", "document_file_id"];
}
