<?php

namespace App\Models;

class CompetitorFile extends BaseModel
{
    protected static Array $unique_fields = ["competitor_id", "document_file_id"];

    public function competitor()
    {
        return $this->belongsTo(Competitor::class);
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
            case "competitor_id":
                $ret_array = [
                    $field => $this->competitor?->getDisplayName()
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
