<?php

namespace App\Models;

use App\Exceptions\UserException;

class StockProduct extends BaseModel
{
    protected static array $unique_fields = ["branch_id", "bin_id", "product_id"];

    public static array $child_model_class = [
        StockProductTransaction::class => [
            "foreignKey" => "stock_product_id",
            "preventDelete" => true
        ]
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function bin()
    {
        return $this->belongsTo(Bin::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "branch_id":
                $ret_array = [
                    $field => $this->branch->getDisplayName()
                ];
                break;
            case "bin_id":
                $ret_array = [
                    $field => $this->bin->getDisplayName()
                ];
                break;
            case "product_id":
                $ret_array = [
                    $field => $this->product->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }

    public function changeQty($branch_id, $bin_id, $product_id, $qty)
    {
        $qb = self::where("branch_id", $branch_id);

        if ($bin_id)
        {
            $qb->where("bin_id", $bin_id);
        }

        $qb->where("product_id", $product_id);
        $model = $qb->first();

        $product = Product::findOrFail($product_id);

        if ($model)
        {
            $avialable_qty = $model->opening_qty + $model->qty;
            if ($avialable_qty + $qty < 0)
            {
                $avialable_qty = round($avialable_qty, 3);
                $msg = "Product " . $product->getDisplayName();
                $msg .= " have qty $avialable_qty only";

                throw new UserException($msg, UserException::ERROR_USER_ACTION_RESTRICT);
            }
            else
            {
                $model->qty += $qty;
                $model->save();
            }
        }
        else
        {
            if ($qty <= 0)
            {
                $msg = "Product " . $product->getDisplayName();
                $msg .= " have no stock";

                throw new UserException($msg, UserException::ERROR_USER_ACTION_RESTRICT);
            }

            $save_data = [
                "branch_id" => $branch_id,
                "bin_id" => $branch_id,
                "product_id" => $branch_id,
                "qty" => $qty
            ];

            $model = new static($save_data);
            $model->save();
        }

        StockProductTransaction::create([
            "stock_product_id" => $model->id,
            "qty" => $qty
        ]);
    }

    public function getAvialiableQty()
    {
        $avialable_qty = $this->opening_qty + $this->qty;

        return round($avialable_qty, 3);
    }
}
