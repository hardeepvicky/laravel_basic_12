<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class DispatchOrderItem extends BaseModel
{
    protected static Array $unique_fields = ["dispatch_order_id", "item_id"];

    public function dispatchOrder()
    {
        return $this->belongsTo(DispatchOrder::class);
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
            "item_id" => [Rule::unique($this->connection . "." . $this->getTable(), "item_id")->where("dispatch_order_id", $validateData["dispatch_order_id"])],
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
