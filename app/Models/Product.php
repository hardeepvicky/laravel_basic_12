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
        ProductAttribute::class => [
            "foreignKey" => ["product_id"],
            "preventDelete" => true
        ]
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function productFile()
    {
        return $this->hasMany(ProductFile::class, 'product_id');
    }

    public function images()
    {
        return $this->belongsToMany(DocumentFile::class, ProductFile::class)->where("file_type", "image");
    }

    public function videos()
    {
        return $this->belongsToMany(DocumentFile::class, ProductFile::class)->where("file_type", "video");
    }

    public function otherFiles()
    {
        return $this->belongsToMany(DocumentFile::class, ProductFile::class)->where("file_type", "");
    }

    public function mainImage()
    {
        return $this->belongsTo(DocumentFile::class);
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
            "model_name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")],
            "sku" => [Rule::unique($this->connection . "." . $this->getTable(), "name")],
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

    public function getFieldLabels()
    {
        $labels = parent::getFieldLabels();

        $labels['productFile'] = "Files";

        return $labels;
    }
}
