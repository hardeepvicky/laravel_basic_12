<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ShipOrder extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected $casts = [
    ];

    protected static array $unique_fields = [];

    public static array $child_model_class = [
        ShipOrderProduct::class => [
            "foreignKey" => ["ship_order_id"],
            "preventDelete" => false
        ],
        ShipOrderItem::class => [
            "foreignKey" => ["ship_order_id"],
            "preventDelete" => false
        ],
    ];

    public const STATUS_PENDING = "PENDING";
    public const STATUS_SENT = "SENT";
    public const STATUS_INTRANSIT = "INTRANSIT";
    public const STATUS_DELIVERED = "DELIVERED";

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
        if ($this->export_order_id)
        {
            $name = $this->exportOrder->order_no;
        }
        else
        {
            $name = $this->docket_no;
        }

        return $name;
    }

    public function orderFrom()
    {
        if ($this->export_order_id)
        {
            return "Export";
        }

        return ucfirst($this->order_from_type);
    }

    public function shipOrderProduct()
    {
        return $this->hasMany(ShipOrderProduct::class, 'ship_order_id');
    }

    public function shipOrderItem()
    {
        return $this->hasMany(ShipOrderItem::class, 'ship_order_id');
    }

    public function country()
    {
        return $this->belongsTo(ExpCountry::class);
    }

    public function state()
    {
        return $this->belongsTo(ExpState::class);
    }

    public function city()
    {
        return $this->belongsTo(ExpCity::class);
    }

    public function carrier()
    {
        return $this->belongsTo(ExpParty::class);
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
            "tracking_no" => [Rule::unique($this->connection . "." . $this->getTable(), "tracking_no")->where("carrier_id", $request->get("carrier_id"))],
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
            case "country_id":
                $ret_array = [
                    $field => $this->country?->getDisplayName()
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

    public function canMarkSent()
    {
        $this->load("shipOrderProduct.shipOrderProductSerial");

        foreach($this->shipOrderProduct as $shipOrderProduct)
        {
            if ($shipOrderProduct->qty != $shipOrderProduct->shipOrderProductSerial->count())
            {
                return false;
            }
        }

        return true;
    }
}
