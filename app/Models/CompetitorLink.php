<?php

namespace App\Models;

class CompetitorLink extends BaseModel
{
    protected static Array $unique_fields = ["competitor_id"];

    public function competitor()
    {
        return $this->belongsTo(Competitor::class);
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
        }

        return $ret_array;
    }
}
