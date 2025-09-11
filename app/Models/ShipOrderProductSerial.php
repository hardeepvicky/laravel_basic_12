<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ShipOrderProductSerial extends BaseModel
{
    protected static Array $unique_fields = ["ship_order_product_id", "product_serial_id"];

    public function shipOrderProduct()
    {
        return $this->belongsTo(ShipOrderProduct::class);
    }

    public function productSerial()
    {
        return $this->belongsTo(ProductSerial::class);
    }

    public function getUniqueValidationRules(array $validateData)
    {
        $field_rules = [
            "product_serial_id" => [Rule::unique($this->connection . "." . $this->getTable(), "product_serial_id")->where("ship_order_product_id", $validateData["ship_order_product_id"])],
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
