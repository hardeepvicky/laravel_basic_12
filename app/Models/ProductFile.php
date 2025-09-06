<?php

namespace App\Models;

class ProductFile extends BaseModel
{
    protected static Array $unique_fields = ["product_id", "document_file_id"];

    public function product()
    {
        return $this->belongsTo(Product::class);
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
            case "product_id":
                $ret_array = [
                    $field => $this->product?->getDisplayName()
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
