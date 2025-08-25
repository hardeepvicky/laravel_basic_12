<?php

namespace App\Models;

class SaleOrderProduct extends BaseModel
{
    protected static Array $unique_fields = ["sale_order_id", "product_id", "make_id"];

    public function saleOrder()
    {
        return $this->belongsTo(SaleOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function maker()
    {
        return $this->belongsTo(Maker::class);
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "sale_order_id":
                $ret_array = [
                    $field => $this->saleOrder?->getDisplayName()
                ];
                break;

            case "product_id":
                $ret_array = [
                    $field => $this->product?->getDisplayName()
                ];
                break;

            case "make_id":
                $ret_array = [
                    $field => $this->maker?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
