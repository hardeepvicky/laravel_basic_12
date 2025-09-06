<?php

namespace App\Models;

class LeadFile extends BaseModel
{
    protected static Array $unique_fields = ["lead_id", "document_file_id"];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
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
            case "lead_id":
                $ret_array = [
                    $field => $this->lead?->getDisplayName()
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
