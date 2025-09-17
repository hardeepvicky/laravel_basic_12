<?php

namespace App\Models;

class PurchaseOrderReceiveItem extends BaseModel
{
    public static array $child_model_class = [        
    ];

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function afterSave()
    {
        $this->purchaseOrderItem->increment('receive_qty', $this->receive_qty);

        return parent::afterSave();
    }

    public function beforeDelete() 
    {
        $this->purchaseOrderItem->decrement('receive_qty', $this->receive_qty);

        return parent::beforeDelete();
    }
}
