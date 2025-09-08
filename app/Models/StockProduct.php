<?php

namespace App\Models;

use App\Exceptions\UserException;

class StockProduct extends BaseModel
{
    protected static array $unique_fields = ["branch_id", "bin_id", "product_id", "make_id"];

    public static array $child_model_class = [
        StockProductTransaction::class => [
            "foreignKey" => "stock_product_id",
            "preventDelete" => true
        ]
    ];

    public function afterFindForCsv()
    {
        parent::afterFind();
    }

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

    public function make()
    {
        return $this->belongsTo(Maker::class);
    }

    public static function changeQty($branch_id, $bin_id, $product_id, $make_id, $qty)
    {
        $qb = self::where("branch_id", $branch_id);

        if ($bin_id)
        {
            $qb->where("bin_id", $bin_id);
        }

        $qb->where("product_id", $product_id);
        $qb->where("make_id", $make_id);
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
                "bin_id" => $bin_id,
                "product_id" => $product_id,
                "make_id" => $make_id,
                "opening_qty" => 0,
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

        $so_available_qty = SaleOrderProduct::where("product_id", $this->product_id)->where("make_id", $this->make_id)->sum("available_qty");
        $so_qty_to_manufacture = SaleOrderProduct::where("product_id", $this->product_id)->where("make_id", $this->make_id)->sum("qty_to_manufacture");
        $so_sent_qty = SaleOrderProduct::where("product_id", $this->product_id)->where("make_id", $this->make_id)->sum("sent_qty");

        $eo_available_qty = ExportOrderProduct::where("product_id", $this->product_id)->where("make_id", $this->make_id)->sum("available_qty");
        $eo_qty_to_manufacture = ExportOrderProduct::where("product_id", $this->product_id)->where("make_id", $this->make_id)->sum("qty_to_manufacture");
        $eo_sent_qty = ExportOrderProduct::where("product_id", $this->product_id)->where("make_id", $this->make_id)->sum("sent_qty");

        $avialable_qty -= $so_available_qty + $so_qty_to_manufacture - $so_sent_qty;
        $avialable_qty -= $eo_available_qty + $eo_qty_to_manufacture - $eo_sent_qty;

        if ($avialable_qty < 0)
        {
            $avialable_qty = 0;
        }

        return round($avialable_qty, 3);
    }
}
