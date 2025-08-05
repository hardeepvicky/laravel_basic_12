<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ProductCatalog extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected static array $unique_fields = ["product_category_id", "name"];

    protected static array $list_fields = ["id", "name", "product_category_id"];

    public static array $child_model_class = [
        Product::class => [
            "foreignKey" => ["product_catalog_id"],
            "preventDelete" => true
        ]
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, "product_category_id");
    }

    public function document()
    {
        return $this->belongsTo(DocumentFile::class, "document_file_id");
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
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")->where("product_category_id", $request->get("product_category_id"))],
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
