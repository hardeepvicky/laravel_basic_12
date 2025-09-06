<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class UserCategory extends BaseModel
{
    use HasFactory;

    public static array $child_model_class = [
        User::class => [
            "foreignKey" => "user_category_id",
            "preventDelete" => true,
        ],
    ];

    protected $appends = [
        'display_name',
    ];

    protected static array $unique_fields = ["type", "name"];

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")->where("type", $request->input("type"))],
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

    public function user()
    {
        return $this->hasMany(User::class, "user_category_id");
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->name;
    }
}
