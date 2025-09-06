<?php

namespace App\Models;

class ExpCompetitorLink extends BaseModel
{
    protected static Array $unique_fields = ["exp_competitor_id"];

    public function expCompetitor()
    {
        return $this->belongsTo(ExpCompetitor::class);
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
        }

        return $ret_array;
    }
}
