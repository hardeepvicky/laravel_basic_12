<?php

namespace App\Models;

class SaleOrderFile extends BaseModel
{
    protected static Array $unique_fields = ["sale_order_id", "document_file_id"];

    public function saleOrder()
    {
        return $this->belongsTo(SaleOrder::class);
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
            case "sale_order_id":
                $ret_array = [
                    $field => $this->saleOrder?->getDisplayName()
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
