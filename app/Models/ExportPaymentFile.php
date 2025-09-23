<?php

namespace App\Models;

class ExportPaymentFile extends BaseModel
{
    protected static Array $unique_fields = ["export_payment_id", "document_file_id"];

    public function exportPayment()
    {
        return $this->belongsTo(ExportPayment::class);
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
            case "export_payment_id":
                $ret_array = [
                    $field => $this->exportPayment?->getDisplayName()
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
