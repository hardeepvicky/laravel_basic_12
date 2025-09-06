<?php

namespace App\Models;

class PartyFile extends BaseModel
{
    protected static Array $unique_fields = ["party_id", "document_file_id"];

    public function party()
    {
        return $this->belongsTo(Party::class);
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
            case "party_id":
                $ret_array = [
                    $field => $this->party?->getDisplayName()
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
