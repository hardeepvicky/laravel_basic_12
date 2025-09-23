<?php

namespace App\Models;

class ExpCompetitorProductCategory extends BaseModel
{
    protected static Array $unique_fields = ["exp_competitor_id", "product_category_id"];

    public function expCompetitor()
    {
        return $this->belongsTo(ExpCompetitor::class);
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
            case "exp_competitor_id":
                $ret_array = [
                    $field => $this->expCompetitor?->getDisplayName()
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
