<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Country extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected static array $list_fields = ["id", "name", "short_name"];

    public static array $child_model_class = [
        State::class => [
            "foreignKey" => ["country_id"],
            "preventDelete" => true
        ], 
        DispatchOrder::class => [
            "foreignKey" => ["country_id"],
            "preventDelete" => true
        ],        
    ];

    public function getUniqueValidationRules()
    {
        $field_rules = [
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")],
            "short_name" => [Rule::unique($this->connection . "." . $this->getTable(), "short_name")],
        ];

        if ($this->id) {
            foreach ($field_rules as $field => $rules) {
                foreach ($rules as $rule) {
                    if ($rule instanceof Unique) {
                        $rule->ignore($this->id, $this->primaryKey);
                    }
                }
            }
        }

        return $field_rules;
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->name . " (" . $this->short_name . ")";
    }

    public static function getMyCountry()
    {
        $record = self::query()->where("name", "india")->first();

        return $record;
    }
}
