<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Product extends BaseModel
{
    protected $appends = [
        'display_name'
    ];

    protected static array $unique_fields = ["product_category_id", "name"];

    public static array $child_model_class = [
        // User::class => [
        //     "foreignKey" => ["sub_department_id"],
        //     "preventDelete" => true
        // ]
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->name;
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")->where("product_category_id", $request->input("product_category_id"))],
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

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "product_category_id":
                $ret_array = [
                    $field => $this->category?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
