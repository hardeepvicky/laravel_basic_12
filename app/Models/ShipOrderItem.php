<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ShipOrderItem extends BaseModel
{
    protected static Array $unique_fields = ["ship_order_id", "item_id"];

    public function shipOrder()
    {
        return $this->belongsTo(ShipOrder::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "item_id":
                $ret_array = [
                    $field => $this->item?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }

    public function getUniqueValidationRules(array $validateData)
    {
        $field_rules = [
            "item_id" => [Rule::unique($this->connection . "." . $this->getTable(), "item_id")->where("ship_order_id", $validateData["ship_order_id"])],
        ];

        if (isset($validateData['id']) && $validateData['id']) {
            foreach ($field_rules as $field => $rules) {
                foreach ($rules as $rule) {
                    if ($rule instanceof Unique) {
                        // d([$field => $this->id]);
                        $rule->ignore($validateData['id'], $this->primaryKey);
                    }
                }
            }
        }

        return $field_rules;
    }
}
