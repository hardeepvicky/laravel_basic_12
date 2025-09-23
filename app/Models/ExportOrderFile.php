<?php

namespace App\Models;

class ExportOrderFile extends BaseModel
{
    protected static Array $unique_fields = ["export_order_id", "document_file_id"];

    public function exportOrder()
    {
        return $this->belongsTo(ExportOrder::class);
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
            case "export_order_id":
                $ret_array = [
                    $field => $this->exportOrder?->getDisplayName()
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
