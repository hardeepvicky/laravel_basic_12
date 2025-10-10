<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ItemBrandPart extends BaseModel
{
    protected $casts = [
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function make()
    {
        return $this->belongsTo(ItemBrand::class, "make_id");
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

            case "make_id":
                $ret_array = [
                    $field => $this->make?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }

    public function getUniqueValidationRules($validateData) : array
    {
        $field_rules = [
            "make_id" => [Rule::unique($this->connection . "." . $this->getTable(), "make_id")->where("item_id", $validateData['item_id'])],
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
