<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class LeadProduct extends BaseModel
{
    protected static Array $unique_fields = ["lead_id", "product_id"];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "lead_id":
                $ret_array = [
                    $field => $this->lead?->getDisplayName()
                ];
                break;
            case "product_id":
                $ret_array = [
                    $field => $this->product?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }

    public function getUniqueValidationRules(array $validateData)
    {
        $field_rules = [
            "product_id" => [Rule::unique($this->connection . "." . $this->getTable(), "product_id")->where("lead_id", $validateData["lead_id"])],
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
