<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class HolidayDate extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected static array $unique_fields = ["date"];

    public static array $child_model_class = [
        // User::class => [
        //     "foreignKey" => ["sub_department_id"],
        //     "preventDelete" => true
        // ]
    ];

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->date;
    }

    public function holiday()
    {
        return $this->belongsTo(Holiday::class);
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "date" => [Rule::unique($this->connection . "." . $this->getTable(), "date")],
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
