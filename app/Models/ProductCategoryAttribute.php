<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ProductCategoryAttribute extends BaseModel
{
    public static array $child_model_class = [
        // User::class => [
        //     "foreignKey" => ["sub_department_id"],
        //     "preventDelete" => true
        // ]
    ];

    const INPUT_TYPE_TEXT = "TEXT";
    const INPUT_TYPE_INT = "INT";
    const INPUT_TYPE_DECIMAL = "DECIMAL";
    const INPUT_TYPE_DROPDOWN = "DROPDOWN";
    const INPUT_TYPE_CHECKBOX = "CHECKBOX";
    const INPUT_TYPE_DATE = "DATE";
    const INPUT_TYPE_DATETIME = "DATETIME";

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->name;
    }

    public function getUniqueValidationRules(array $validateData)
    {
        $field_rules = [
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")->where("product_category_id", $validateData["product_category_id"])],
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

    
    public static function getCodeList(String $field)
    {
        $class_info = get_class_info(static::class);


        switch ($field) {
            case "input_type":

                $list = [];

                foreach ($class_info['constants'] as $constant => $label) {
                    if (strpos($constant, "INPUT_TYPE_") !== false) {
                        $list[] = $label;
                    }
                }

                return $list;

                break;
        }
    }
}
