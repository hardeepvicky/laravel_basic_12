<?php

namespace App\Models;

class CarrierFile extends BaseModel
{
    protected static Array $unique_fields = ["carrier_id", "document_file_id"];

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
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
            case "carrier_id":
                $ret_array = [
                    $field => $this->carrier?->getDisplayName()
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
