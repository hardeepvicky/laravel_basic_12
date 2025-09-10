<?php

namespace App\Models;

class ExportOrderProduct extends BaseModel
{
    protected static Array $unique_fields = ["export_order_id", "product_id", "make_id"];

    public static array $child_model_class = [
        ManufactureOrder::class => [
            "foreignKey" => "export_order_product_id",
            "preventDelete" => false
        ],
    ];

    public function exportOrder()
    {
        return $this->belongsTo(ExportOrder::class);
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
            case "export_order_id":
                $ret_array = [
                    $field => $this->exportOrder?->getDisplayName()
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
