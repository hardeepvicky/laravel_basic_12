<?php

namespace App\Models;

use App\Exceptions\UserException;
use App\Helpers\LaravelExtend;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Payment extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected static array $unique_fields = ["voucher_no"];

    protected static array $list_fields = ["id", "voucher_no"];

    public static array $child_model_class = [        
        PaymentFile::class => [
            "foreignKey" => ["payment_id"],
            "preventDelete" => false
        ],        
    ];

    protected static array $fields_containg_tags = ["description"];

    public string $field_for_create_tag = "display_name";

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
        return $this->belongsToMany(DocumentFile::class, PaymentFile::class);
    }

    public function paymentFile()
    {
        return $this->hasMany(PaymentFile::class, 'payment_id');
    }

    public function contact()
    {
        return $this->belongsTo(ViewContact::class, "contact_id");
    }

    public function saleOrder()
    {
        return $this->belongsTo(SaleOrder::class);
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
