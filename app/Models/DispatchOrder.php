<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class DispatchOrder extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected $casts = [
    ];

    protected static array $unique_fields = [];

    public static array $child_model_class = [
        LeadProduct::class => [
            "foreignKey" => ["dispatch_order_id"],
            "preventDelete" => false
        ],
        LeadItem::class => [
            "foreignKey" => ["dispatch_order_id"],
            "preventDelete" => false
        ],
    ];

    public const STATUS_PENDING = "PENDING";
    public const STATUS_SENT = "SENT";
    public const STATUS_INTRANSIT = "INTRANSIT";
    public const STATUS_DELIVERED = "DELIVERED";


    public function beforeSave()
    {
        if ($this->fee && $this->fee_gst_per)
        {
            $this->fee_gst = round($this->fee * $this->fee_gst_per / 100, 2);
        }

        if ($this->fee && $this->fee_gst) {
            $this->total_fee = $this->fee + $this->fee_gst;
        }
    }

    
    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        $name = $this->docket_no;
        
        return $name;
    }

    public function products()
    {
        return $this->hasMany(DispatchOrderProduct::class, 'dispatch_order_id');
    }

    public function items()
    {
        return $this->hasMany(DispatchOrderItem::class, 'dispatch_order_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "tracking_no" => [Rule::unique($this->connection . "." . $this->getTable(), "tracking_no")],
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

    public static function getCodeList(String $field)
    {
        $class_info = get_class_info(static::class);

        switch ($field) {
            case "status_type":
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

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "state_id":
                $ret_array = [
                    $field => $this->state?->getDisplayName()
                ];
                break;
            case "district_id":
                $ret_array = [
                    $field => $this->district?->getDisplayName()
                ];
                break;
            case "city_id":
                $ret_array = [
                    $field => $this->city?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
