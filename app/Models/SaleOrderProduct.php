<?php

namespace App\Models;

class SaleOrderProduct extends BaseModel
{
    protected static Array $unique_fields = ["sale_order_id", "product_id", "make_id"];

    public static array $child_model_class = [
        ManufactureOrder::class => [
            "foreignKey" => "sale_order_product_id",
            "preventDelete" => false
        ],
    ];

    public function saleOrder()
    {
        return $this->belongsTo(SaleOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function make()
    {
        return $this->belongsTo(Maker::class);
    }

    public function manufactureOrder()
    {
        return $this->hasOne(ManufactureOrder::class);
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

    public function pendingOrderQty()
    {
        $pending_order_qty = $this->order_qty - $this->sent_qty;

        if ($pending_order_qty <= 0)
        {
            return 0;
        }

        $pending_qty = $this->available_qty;
        $manufactured_qty = ManufactureOrder::where("sale_order_product_id", $this->id)->sum("manufactured_qty");

        $pending_qty += $manufactured_qty;

        if ($pending_qty > $pending_order_qty)
        {
            $pending_qty = $pending_order_qty;
        }

        return $pending_qty;
    }
}
