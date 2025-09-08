<?php

namespace App\Models;

use App\Helpers\DateUtility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ExportOrder extends BaseModel
{
    protected $appends = [
        'display_name'
    ];

    protected $casts = [
        'estimated_dispatch_date' => 'date:' . DateUtility::DATE_OUT_FORMAT,
        'dispatch_date_time' => 'date:' . DateUtility::DATE_OUT_FORMAT,
    ];

    protected static array $unique_fields = ["order_no"];

    protected static array $list_fields = ["id", "order_no"];

    const ORDER_FROM_TYPE_DEALER = "DEALER";
    const ORDER_FROM_TYPE_CUSTOMER = "CUSTOMER";
    const ORDER_FROM_TYPE_LEAD = "LEAD";

    const STATUS_NEW = "NEW";
    const STATUS_PRODUCTION = "PRODUCTION";
    const STATUS_GODOWN = "GODOWN";

    public static array $child_model_class = [
        ExportOrderProduct::class => [
            "foreignKey" => "export_order_id",
            "preventDelete" => false
        ],
        ExportOrderFile::class => [
            "foreignKey" => "export_order_id",
            "preventDelete" => false
        ],
    ];

    public function afterFindForCsv()
    {
        parent::afterFind();
    }

    public function afterSave()
    {
        Order::createOrder("export", $this->id);

        return parent::afterSave();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function contact()
    {
        return $this->belongsTo(ViewExportContact::class, "export_contact_id");
    }

    public function customer()
    {
        return $this->belongsTo(ExpCustomer::class);
    }

    public function carrier()
    {
        return $this->belongsTo(ExpParty::class);
    }

    public function exportOrderProduct()
    {
        return $this->hasMany(ExportOrderProduct::class, 'export_order_id');
    }

    public function files()
    {
        return $this->belongsToMany(DocumentFile::class, ExportOrderFile::class);
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
            case "export_contact_id":
                $ret_array = [
                    $field => $this->contact?->getDisplayName()
                ];
                break;
            case "exp_customer_id":
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

    public static function getOrderFromTypeList()
    {
        return [
            self::ORDER_FROM_TYPE_DEALER => "Dealer",
            self::ORDER_FROM_TYPE_CUSTOMER => "Customer",
            self::ORDER_FROM_TYPE_LEAD => "New  Customer (Lead)",
        ];
    }

    
    public static function getCodeList(String $field)
    {
        $class_info = get_class_info(static::class);


        switch ($field) {
            case "status":
                $list = [];

                foreach ($class_info['constants'] as $constant => $label) {
                    if (strpos($constant, "STATUS_") !== false) {
                        $list[] = $label;
                    }
                }

                return $list;
                break;
        }
    }
}
