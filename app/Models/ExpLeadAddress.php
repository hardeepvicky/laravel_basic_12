<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ExpLeadAddress extends BaseModel
{
    protected static Array $unique_fields = ["exp_lead_id", "addr1"];

    const TYPE_ADDR_HOME = "ADDR_HOME";
    const TYPE_ADDR_OFFICE = "ADDR_OFFICE";

    public function expLead()
    {
        return $this->belongsTo(ExpLead::class);
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "exp_lead_id":
                $ret_array = [
                    $field => $this->expLead?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }

    public function getUniqueValidationRules(array $validateData)
    {
        $field_rules = [
            "addr1" => [Rule::unique($this->connection . "." . $this->getTable(), "addr1")->where("exp_lead_id", $validateData["exp_lead_id"])],
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

    public static function getTypeList()
    {
        return [
            self::TYPE_ADDR_HOME => "Home",
            self::TYPE_ADDR_OFFICE => "Office"
        ];
    }
}
