<?php

namespace App\Models;

use App\Exceptions\UserException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class SaleOrder extends BaseModel
{
    protected $appends = [
        'display_name'
    ];

    protected static array $unique_fields = ["order_no"];

    protected static array $list_fields = ["id", "order_no"];

    public static array $child_model_class = [
        
    ];

    public function afterFindForCsv()
    {
        parent::afterFind();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function carrier()
    {
        return $this->belongsTo(Party::class);
    }

    public function saleOrderProduct()
    {
        return $this->hasMany(SaleOrderProduct::class, 'sale_order_id');
    }

    public function files()
    {
        return $this->belongsToMany(DocumentFile::class, SaleOrderFile::class);
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->order_no;
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "order_no" => [Rule::unique($this->connection . "." . $this->getTable(), "order_no")],
        ];

        if ($this->id) {
            foreach ($field_rules as $field => $rules) {
                foreach ($rules as $rule) {
                    if ($rule instanceof Unique) {
                        // d([$field => $this->id]);
                        $rule->ignore($this->id, $this->primaryKey);
                    }
                }
            }
        }

        return $field_rules;
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "branch_id":
                $ret_array = [
                    $field => $this->branch?->getDisplayName()
                ];
                break;
            case "dealer_id":
                $ret_array = [
                    $field => $this->dealer?->getDisplayName()
                ];
                break;
            case "customer_id":
                $ret_array = [
                    $field => $this->customer?->getDisplayName()
                ];
                break;
            case "carrier_id":
                $ret_array = [
                    $field => $this->carrier?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }

    public function getFieldLabels()
    {
        $labels = parent::getFieldLabels();

        return $labels;
    }
}
