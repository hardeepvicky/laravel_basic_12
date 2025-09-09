<?php

namespace App\Models;

use App\Helpers\DateUtility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ManufactureOrder extends BaseModel
{
    const STATUS_PENDING = "PENDING";
    const STATUS_IN_PROGRESS = "IN_PROGRESS";
    const STATUS_COMPLETE = "COMPLETE";

    protected $casts = [
        'order_date_time' => 'datetime:' . DateUtility::DATETIME_OUT_FORMAT,
        'manufacture_complete_date_time' => 'datetime:' . DateUtility::DATETIME_OUT_FORMAT,
    ];

    public static array $child_model_class = [
        // User::class => [
        //     "foreignKey" => ["sub_dept_id"],
        //     "preventDelete" => true
        // ]
    ];

    public function beforeSave()
    {
        if ($this->manufactured_qty)
        {
            if ($this->manufactured_qty >= $this->order_qty)
            {
                $this->status = self::STATUS_COMPLETE;
            }
            else
            {
                $this->status = self::STATUS_IN_PROGRESS;
            }
        }
        else
        {
            $this->status = self::STATUS_PENDING;
        }

        return parent::beforeSave();
    }

    public function afterFindForCsv()
    {
        parent::afterFind();
    }

    public function saleOrderProduct()
    {
        return $this->belongsTo(SaleOrderProduct::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function make()
    {
        return $this->belongsTo(Maker::class);
    }

    public function assignTo()
    {
        return $this->belongsTo(User::class, "assign_to_user_id");
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->name;
    }

    public function getUniqueValidationRules(Request $request)
    {
        return [];
    }

    public function getRequiedIfRules(Request $request)
    {
        $rules = [];

        if ($this->order_qty > 0 && $request->input("manufactured_qty", 0) == $this->order_qty)
        {
            $rules["manufacture_complete_date_time"] = ["required"];
            $rules["manufacture_complete_by"] = ["required"];
        }

        return $rules;
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "product_id":
                $ret_array = [
                    $field => $this->product?->getDisplayName()
                ];
                break;
            case "make_id":
                $ret_array = [
                    $field => $this->make?->getDisplayName()
                ];
                break;
            case "assign_to_user_id":
                $ret_array = [
                    $field => $this->assignTo?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_PENDING => "Pending",
            self::STATUS_IN_PROGRESS => "In-Progress",
            self::STATUS_COMPLETE => "Completed",
        ];
    }

    public function increaseManufactureQty($qty = 1)
    {
        $this->manufactured_qty += $qty;
        $this->save();
    }
}
