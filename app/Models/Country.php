<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Country extends BaseModel
{
    public array $child_model_class = [];

    protected $appends = [
        'display_name',
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
        return $this->name . " (" . $this->sortname . ")";
    }

    public static function getMyCountry()
    {
        $record = self::query()->where("name", "india")->first();

        return $record;
    }
}
