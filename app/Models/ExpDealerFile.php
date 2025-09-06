<?php

namespace App\Models;

class ExpDealerFile extends BaseModel
{
    protected static Array $unique_fields = ["exp_dealer_id", "document_file_id"];

    public function expDealer()
    {
        return $this->belongsTo(ExpDealer::class);
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
            case "exp_dealer_id":
                $ret_array = [
                    $field => $this->expDealer?->getDisplayName()
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
