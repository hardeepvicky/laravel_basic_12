<?php

namespace App\Models;

class TaskFile extends BaseModel
{
    protected static Array $unique_fields = ["task_id", "document_file_id"];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function documentFile()
    {
        return $this->belongsTo(DocumentFile::class);
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

            case "document_file_id":
                $ret_array = [
                    $field => $this->documentFile->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
