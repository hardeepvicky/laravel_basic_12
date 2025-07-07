<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Tag extends BaseModel
{
    public static array $child_model_class = [
       
    ];

    protected $appends = [
        'display_name',
    ];

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->search_term;
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "search_term" => [Rule::unique($this->connection . "." . $this->getTable(), "name")],
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
