<?php

namespace App\Models;

use App\Exceptions\UserException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Product extends BaseModel
{
    protected $appends = [
        'display_name'
    ];

    protected static array $unique_fields = ["product_category_id", "name"];

    protected static array $fields_containg_tags = ["description"];

    public string $field_for_create_tag = "model_name";

    public static array $child_model_class = [
        ProductAttribute::class => [
            "foreignKey" => ["product_id"],
            "preventDelete" => true
        ]
    ];

    public function beforeSave()
    {
        parent::beforeSave();

        if ($this->size_uom_id)
        {
            if ($this->length)
            {
                $this->length = Uom::convertToParentUomValue($this->size_uom_id, $this->length);
            }
            if ($this->width)
            {
                $this->width = Uom::convertToParentUomValue($this->size_uom_id, $this->width);
            }
            if ($this->height)
            {
                $this->height = Uom::convertToParentUomValue($this->size_uom_id, $this->height);
            }
        }
    }
    
    public function afterFind()
    {
        parent::afterFind();

        $this->parseTagsForModuleLinking();
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function catalog()
    {
        return $this->belongsTo(ProductCatalog::class, "product_catalog_id");
    }

    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function productFile()
    {
        return $this->hasMany(ProductFile::class, 'product_id');
    }

    public function keywords()
    {
        return $this->hasMany(ProductKeyword::class, 'product_id');
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

    public function convertToChildUom()
    {
        if ($this->size_uom_id)
        {
            if ($this->length)
            {
                $this->length = Uom::convertToChildUomValue($this->size_uom_id, $this->length);
            }
            if ($this->width)
            {
                $this->width = Uom::convertToChildUomValue($this->size_uom_id, $this->width);
            }
            if ($this->height)
            {
                $this->height = Uom::convertToChildUomValue($this->size_uom_id, $this->height);
            }
        }
    }

    public static function convertRecordsForChildUom($records)
    {
        $cache_key = "all_uoms";

        $uoms = static::getCache($cache_key);

        if (!$uoms)
        {
            $temp = Uom::get()->toArray();

            foreach($temp as $arr)
            {
                $uoms[$arr['id']] = $arr;
            }

            if ($uoms)
            {                
                Uom::addCache($cache_key, $uoms, 60 * 60 * 24);
            }
        }

        foreach($records as $record)
        {
            if ($record->size_uom_id && isset($uoms[$record->size_uom_id]))
            {
                $record->size_uom_name = $uoms[$record->size_uom_id]['short_name'];
                if ($uoms[$record->size_uom_id]['conversion_factor'])
                {
                    if ($record->length)
                    {
                        $record->length *= $uoms[$record->size_uom_id]['conversion_factor'];
                        $record->length = round($record->length, 3);
                    }

                    if ($record->width)
                    {
                        $record->width *= $uoms[$record->size_uom_id]['conversion_factor'];

                        $record->width = round($record->width, 3);
                    }

                    if ($record->height)
                    {
                        $record->height *= $uoms[$record->size_uom_id]['conversion_factor'];
                        $record->height = round($record->height, 3);
                    }
                }
            }
        }
    }
}
