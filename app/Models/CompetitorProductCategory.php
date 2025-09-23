<?php

namespace App\Models;

class CompetitorProductCategory extends BaseModel
{
    protected static Array $unique_fields = ["competitor_id", "product_category_id"];

    public function competitor()
    {
        return $this->belongsTo(Competitor::class);
    }

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
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

            case "product_category_id":
                $ret_array = [
                    $field => $this->productCategory?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
