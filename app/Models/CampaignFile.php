<?php

namespace App\Models;

class CampaignFile extends BaseModel
{
    protected static Array $unique_fields = ["campaign_id", "document_file_id"];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
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
            case "campaign_id":
                $ret_array = [
                    $field => $this->campaign?->getDisplayName()
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
