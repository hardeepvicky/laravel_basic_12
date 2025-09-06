<?php

namespace App\Models;

class ExpPartyFile extends BaseModel
{
    protected static Array $unique_fields = ["exp_party_id", "document_file_id"];

    public function expParty()
    {
        return $this->belongsTo(ExpParty::class);
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
            case "exp_party_id":
                $ret_array = [
                    $field => $this->expParty?->getDisplayName()
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
