<?php

namespace App\Models;

class BomItem extends BaseModel
{
    protected static Array $unique_fields = ["item_id", "bom_id"];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function bom()
    {
        return $this->belongsTo(Bom::class);
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

            case "bom_id":
                $ret_array = [
                    $field => $this->bom?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
