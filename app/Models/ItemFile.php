<?php

namespace App\Models;

class ItemFile extends BaseModel
{
    protected static Array $unique_fields = ["item_id", "document_file_id"];

    public function item()
    {
        return $this->belongsTo(Item::class);
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
            case "item_id":
                $ret_array = [
                    $field => $this->item?->getDisplayName()
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
