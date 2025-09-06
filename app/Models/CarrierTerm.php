<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class CarrierTerm extends BaseModel
{
    protected static Array $unique_fields = ["carrier_id", "name"];

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "carrier_id":
                $ret_array = [
                    $field => $this->carrier?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }

    public function getUniqueValidationRules(array $validateData)
    {
        $field_rules = [
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")->where("carrier_id", $validateData["carrier_id"])],
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
}
