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

    protected static array $list_fields = ["id", "name", "model_name", "sku"];

    protected static array $fields_containg_tags = ["description"];

    public string $field_for_create_tag = "model_name";

    public static array $child_model_class = [
        ProductAttribute::class => [
            "foreignKey" => ["product_id"],
            "preventDelete" => true
        ],
        ProductFile::class => [
            "foreignKey" => ["product_id"],
            "preventDelete" => false
        ],
        ProductKeyword::class => [
            "foreignKey" => ["product_id"],
            "preventDelete" => false
        ],
        ProductAttribute::class => [
            "foreignKey" => ["product_id"],
            "preventDelete" => false
        ],
        ProductSpecification::class => [
            "foreignKey" => ["product_id"],
            "preventDelete" => false
        ],
        LeadProduct::class => [
            "foreignKey" => ["product_id"],
            "preventDelete" => true
        ],
        ProductSerial::class => [
            "foreignKey" => ["product_id"],
            "preventDelete" => true
        ],        
        ShipOrderProduct::class => [
            "foreignKey" => ["product_id"],
            "preventDelete" => true
        ],        
        DispatchOrderProduct::class => [
            "foreignKey" => ["product_id"],
            "preventDelete" => true
        ],        
        SaleOrderProduct::class => [
            "foreignKey" => ["product_id"],
            "preventDelete" => true
        ],        
        ExportOrderProduct::class => [
            "foreignKey" => ["product_id"],
            "preventDelete" => true
        ],
        Bom::class => [
            "foreignKey" => ["product_id"],
            "preventDelete" => true
        ],
        StockProduct::class => [
            "foreignKey" => ["product_id"],
            "preventDelete" => true
        ],
    ];

    public function beforeSave()
    {
        parent::beforeSave();

        $this->convertToParentUom();
    }
    
    public function afterFind()
    {
        parent::afterFind();

        $this->parseTagsForModuleLinking();
    }

    public function afterFindForCsv()
    {
        parent::afterFind();
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, "product_category_id");
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

    public function specifications()
    {
        return $this->hasMany(ProductSpecification::class, 'product_id');
    }

    public function stockProduct()
    {
        return $this->hasMany(StockProduct::class, 'product_id');
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

    public function catalog()
    {
        return $this->belongsTo(DocumentFile::class, "catalog_document_file_id");
    }

    public function installationGuide()
    {
        return $this->belongsTo(DocumentFile::class, "installation_guide_document_file_id");
    }

    public function userManual()
    {
        return $this->belongsTo(DocumentFile::class, "user_manual_document_file_id");
    }

    public function troubleshoot()
    {
        return $this->belongsTo(DocumentFile::class, "troubleshoot_document_file_id");
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->model_name;
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
        $labels['images'] = "Images";
        $labels['videos'] = "Videos";
        $labels['other_files'] = "Other Files";

        return $labels;
    }

    public function convertToParentUom()
    {
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

            if ($this->gross_length)
            {
                $this->gross_length = Uom::convertToParentUomValue($this->size_uom_id, $this->gross_length);
            }
            if ($this->gross_width)
            {
                $this->gross_width = Uom::convertToParentUomValue($this->size_uom_id, $this->gross_width);
            }
            if ($this->gross_height)
            {
                $this->gross_height = Uom::convertToParentUomValue($this->size_uom_id, $this->gross_height);
            }
        }
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

            if ($this->gross_length)
            {
                $this->gross_length = Uom::convertToChildUomValue($this->size_uom_id, $this->gross_length);
            }
            if ($this->gross_width)
            {
                $this->gross_width = Uom::convertToChildUomValue($this->size_uom_id, $this->gross_width);
            }
            if ($this->gross_height)
            {
                $this->gross_height = Uom::convertToChildUomValue($this->size_uom_id, $this->gross_height);
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

                    if ($record->gross_length)
                    {
                        $record->gross_length *= $uoms[$record->size_uom_id]['conversion_factor'];
                        $record->gross_length = round($record->gross_length, 3);
                    }

                    if ($record->gross_width)
                    {
                        $record->gross_width *= $uoms[$record->size_uom_id]['conversion_factor'];

                        $record->gross_width = round($record->gross_width, 3);
                    }

                    if ($record->gross_height)
                    {
                        $record->gross_height *= $uoms[$record->size_uom_id]['conversion_factor'];
                        $record->gross_height = round($record->gross_height, 3);
                    }
                }
            }
        }
    }
}
