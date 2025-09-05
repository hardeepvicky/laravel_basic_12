<?php

namespace App\Models;

class ExpLeadFile extends BaseModel
{
    protected static Array $unique_fields = ["exp_lead_id", "document_file_id"];

    public function expLead()
    {
        return $this->belongsTo(ExpLead::class);
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
            case "exp_lead_id":
                $ret_array = [
                    $field => $this->expLead?->getDisplayName()
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
