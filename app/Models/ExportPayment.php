<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ExportPayment extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected static array $unique_fields = ["voucher_no"];

    protected static array $list_fields = ["id", "voucher_no"];

    public static array $child_model_class = [        
        ExportPaymentFile::class => [
            "foreignKey" => ["export_payment_id"],
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
        return $this->belongsToMany(DocumentFile::class, ExportPaymentFile::class);
    }

    public function paymentFile()
    {
        return $this->hasMany(ExportPaymentFile::class, 'payment_id');
    }

    public function contact()
    {
        return $this->belongsTo(ViewExportContact::class, "export_contact_id");
    }

    public function exportOrder()
    {
        return $this->belongsTo(ExportOrder::class);
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
