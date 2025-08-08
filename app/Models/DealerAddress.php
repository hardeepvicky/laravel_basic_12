<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class DealerAddress extends BaseModel
{
    protected static Array $unique_fields = ["dealer_id", "addr1"];

    const TYPE_ADDR_HOME = "ADDR_HOME";
    const TYPE_ADDR_OFFICE = "ADDR_OFFICE";

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "dealer_id":
                $ret_array = [
                    $field => $this->dealer?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }

    public function getUniqueValidationRules(array $validateData)
    {
        $field_rules = [
            "addr1" => [Rule::unique($this->connection . "." . $this->getTable(), "addr1")->where("dealer_id", $validateData["dealer_id"])],
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

    public static function getTypeList()
    {
        return [
            self::TYPE_ADDR_HOME => "Home",
            self::TYPE_ADDR_OFFICE => "Office"
        ];
    }
}
