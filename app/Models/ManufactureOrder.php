<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ManufactureOrder extends BaseModel
{
    public static array $child_model_class = [
        // User::class => [
        //     "foreignKey" => ["sub_dept_id"],
        //     "preventDelete" => true
        // ]
    ];

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
        }

        return $ret_array;
    }
}
