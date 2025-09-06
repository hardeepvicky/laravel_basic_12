<?php

namespace App\Models;

class ExpCompetitorFile extends BaseModel
{
    protected static Array $unique_fields = ["exp_competitor_id", "document_file_id"];

    public function expCompetitor()
    {
        return $this->belongsTo(ExpCompetitor::class);
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
            case "exp_competitor_id":
                $ret_array = [
                    $field => $this->expCompetitor?->getDisplayName()
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
