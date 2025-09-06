<?php

namespace App\Models;

class DealerFile extends BaseModel
{
    protected static Array $unique_fields = ["dealer_id", "document_file_id"];

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
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
            case "dealer_id":
                $ret_array = [
                    $field => $this->dealer?->getDisplayName()
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
