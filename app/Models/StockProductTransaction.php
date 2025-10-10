<?php

namespace App\Models;

use App\Exceptions\UserException;

class StockProductTransaction extends BaseModel
{
    protected static array $unique_fields = ["stock_product_id"];

    public static array $child_model_class = [
        // User::class => [
        //     "foreignKey" => ["sub_dept_id"],
        //     "preventDelete" => true
        // ]
    ];

    public function beforeDelete() 
    {
        $this->load("stockProduct");

        $change_qty = (-1 * $this->qty);

        $this->stockProduct->qty += $change_qty;        

        if ($this->stockProduct->qty < 0)
        {
            $product = Product::findOrFail($this->stockProduct->product_id);

            $avialable_qty = $this->stockProduct->opening_qty + $this->stockProduct->qty;

            $avialable_qty = round($avialable_qty, 3);
            $msg = "Product " . $product->getDisplayName();
            $msg .= " have qty $avialable_qty only, so can not reduce qty of $change_qty";

            throw new UserException($msg, UserException::ERROR_USER_ACTION_RESTRICT);
        }

        $this->stockProduct->save();

        parent::beforeDelete();
    }

    public function stockProduct()
    {
        return $this->belongsTo(StockProduct::class);
    }
}
