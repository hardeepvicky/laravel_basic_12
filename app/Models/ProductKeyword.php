<?php

namespace App\Models;

class ProductKeyword extends BaseModel
{
    protected static Array $unique_fields = ["product_id", "name"];

    public function product()
    {
        return $this->belongsTo(Product::class);
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
        }

        return $ret_array;
    }
}
