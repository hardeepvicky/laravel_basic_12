<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class PurchaseOrderPayment extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected static array $unique_fields = ["voucher_no"];

    protected static array $list_fields = ["id", "voucher_no"];

    public static array $child_model_class = [
        PurchaseOrderPaymentFile::class => [
            "foreignKey" => ["payment_id"],
            "preventDelete" => false
        ],
    ];

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        $name = $this->voucher_no;

        return $name;
    }

    public function afterFindForCsv()
    {
        parent::afterFind();
    }

    public function documentFile()
    {
        return $this->belongsToMany(DocumentFile::class, PurchaseOrderPaymentFile::class);
    }

    public function paymentFile()
    {
        return $this->hasMany(PurchaseOrderPayment::class, 'purchase_order_payment_id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "voucher_no" => [Rule::unique($this->connection . "." . $this->getTable(), "voucher_no")],
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


    public function getFieldLabels()
    {
        $labels = parent::getFieldLabels();
        $labels['files'] = "Files";

        return $labels;
    }
}
