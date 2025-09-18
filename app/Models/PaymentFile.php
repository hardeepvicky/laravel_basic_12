<?php

namespace App\Models;

class PaymentFile extends BaseModel
{
    protected static Array $unique_fields = ["payment_id", "document_file_id"];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
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
            case "payment_id":
                $ret_array = [
                    $field => $this->payment?->getDisplayName()
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
