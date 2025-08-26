<?php

namespace App\Models;

use App\Exceptions\UserException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

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

        parent::beforeDelete();
    }

    public function stockProduct()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "stock_product_id":
                $ret_array = [
                    $field => $this->stockProduct->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
