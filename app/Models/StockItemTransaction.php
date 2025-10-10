<?php

namespace App\Models;

use App\Exceptions\UserException;

class StockItemTransaction extends BaseModel
{
    protected static array $unique_fields = ["stock_item_id"];

    public static array $child_model_class = [
        // User::class => [
        //     "foreignKey" => ["sub_dept_id"],
        //     "preventDelete" => true
        // ]
    ];

    public function beforeDelete() 
    {
        $this->load("stockItem");

        $change_qty = (-1 * $this->qty);

        $this->stockItem->qty += $change_qty;        

        if ($this->stockItem->qty < 0)
        {
            $item = Item::findOrFail($this->stockItem->item_id);

            $avialable_qty = $this->stockItem->opening_qty + $this->stockItem->qty;

            $avialable_qty = round($avialable_qty, 3);
            $msg = "Item " . $item->getDisplayName();
            $msg .= " have qty $avialable_qty only, so can not reduce qty of $change_qty";

            throw new UserException($msg, UserException::ERROR_USER_ACTION_RESTRICT);
        }

        $this->stockItem->save();

        parent::beforeDelete();
    }

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }
}
