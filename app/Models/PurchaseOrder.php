<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class PurchaseOrder extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected $casts = [
    ];

    protected static array $unique_fields = [];

    public static array $child_model_class = [        
        PurchaseOrderItem::class => [
            "foreignKey" => ["purchase_order_id"],
            "preventDelete" => false
        ],
    ];

    public const STATUS_PENDING = "PENDING";
    public const STATUS_APPROVE = "APPROVE";
    public const STATUS_DECLINE = "DECLINE";
    public const STATUS_RECEIVED = "RECEIVED";

    public function afterFindForCsv()
    {
        parent::afterFind();
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->order_no;
    }

    public function purchaseOrderItem()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
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

    public function getRequiedIfRules(Request $request)
    {
        $rules = [];

        return $rules;
    }

    public static function getStatusTypeList()
    {
        return [
            self::STATUS_PENDING => "Pending",
            self::STATUS_APPROVE => "Approve",
            self::STATUS_DECLINE => "Decline",
            self::STATUS_RECEIVED => "Received",
        ];
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
            case "party_id":
                $ret_array = [
                    $field => $this->party?->getDisplayName()
                ];
                break;            
        }

        return $ret_array;
    }

    public function canMarkReceived()
    {
        $this->load("purchaseOrderItem");

        foreach($this->purchaseOrderItem as $purchaseOrderItem)
        {
            if ($purchaseOrderItem->qty > $purchaseOrderItem->receive_qty)
            {
                return false;
            }
        }

        return true;
    }
}

