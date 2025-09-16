<?php

namespace App\Models;

class PurchaseOrderRequirement extends BaseModel
{
    protected static Array $unique_fields = ["purchase_order_id", "requirement_id"];

    public static array $child_model_class = [
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function requirement()
    {
        return $this->belongsTo(Requirement::class, "requirement_id");
    }

    public function make()
    {
        return $this->belongsTo(Maker::class);
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "purchase_order_id":
                $ret_array = [
                    $field => $this->purchaseOrder?->getDisplayName()
                ];
                break;

            case "requirement_id":
                $ret_array = [
                    $field => $this->requirement?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }

    public function pendingQty()
    {
        $pending_qty = $this->qty - $this->receive_qty;

        if ($pending_qty <= 0)
        {
            return 0;
        }

        return $pending_qty;
    }
}
