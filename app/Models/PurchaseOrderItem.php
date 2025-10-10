<?php

namespace App\Models;

class PurchaseOrderItem extends BaseModel
{
    protected static Array $unique_fields = ["purchase_order_id", "item_id", "make_id"];

    public static array $child_model_class = [
        PurchaseOrderReceiveItem::class => [
            "foreignKey" => ["purchase_order_item_id"],
            "preventDelete" => true
        ],
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function make()
    {
        return $this->belongsTo(ItemBrand::class, "item_brand_id");
    }

    public function purchaseOrderReceiveItem()
    {
        return $this->hasMany(PurchaseOrderReceiveItem::class);
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

            case "item_id":
                $ret_array = [
                    $field => $this->item?->getDisplayName()
                ];
                break;

            case "make_id":
                $ret_array = [
                    $field => $this->make?->getDisplayName()
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
