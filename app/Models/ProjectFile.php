<?php

namespace App\Models;

class ProjectFile extends BaseModel
{
    protected static Array $unique_fields = ["project_id", "document_file_id"];

    public function project()
    {
        return $this->belongsTo(Project::class);
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
            case "project_id":
                $ret_array = [
                    $field => $this->project?->getDisplayName()
                ];
                break;

            case "document_file_id":
                $ret_array = [
                    $field => $this->documentFile?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
