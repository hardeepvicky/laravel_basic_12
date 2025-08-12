<?php

namespace App\Models;

use App\Exceptions\UserException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Item extends BaseModel
{
    protected $appends = [
        'display_name'
    ];

    protected static array $unique_fields = ["item_category_id", "name"];

    protected static array $list_fields = ["id", "name", "sku"];

    protected static array $fields_containg_tags = ["description"];

    public string $field_for_create_tag = "sku";

    public static array $child_model_class = [
        ItemFile::class => [
            "foreignKey" => ["item_id"],
            "preventDelete" => false
        ],
    ];

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
        return $this->belongsTo(ItemCategory::class, "item_category_id");
    }

    public function itemFile()
    {
        return $this->hasMany(ItemFile::class, 'item_id');
    }

    public function images()
    {
        return $this->belongsToMany(DocumentFile::class, ItemFile::class)->where("file_type", "image");
    }

    public function otherFiles()
    {
        return $this->belongsToMany(DocumentFile::class, ItemFile::class)->where("file_type", "");
    }

    public function mainImage()
    {
        return $this->belongsTo(DocumentFile::class);
    }

    public function color()
    {
        return $this->belongsTo(DropDownValue::class);
    }

    public function brand()
    {
        return $this->belongsTo(DropDownValue::class);
    }

    public function tags()
    {
        $class_name = str_class_name_without_namespace($this);

        return $this->hasMany(Tag::class, 'foreign_id')->where("foreign_model", $class_name);
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        $name = $this->name;

        if ($this->sku)
        {
            $name .= " (" . $this->sku . ")";
        }

        return $name;
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
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
            case "item_category_id":
                $ret_array = [
                    $field => $this->item?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }

    public function getFieldLabels()
    {
        $labels = parent::getFieldLabels();

        $labels['tag_name'] = "Tags";
        $labels['itemFile'] = "Files";
        $labels['images'] = "Images";
        $labels['other_files'] = "Other Files";

        return $labels;
    }

    public static function getSkuFromPattern(string $sku_pattern, array $record) : string
    {
        $item_category_short_list = ItemCategory::getListCache("id", "short_name");
        $brand_short_name_list = DropDownValue::getList("id", "short_name", ["type" => DropDownValue::BRAND]);
        $color_short_name_list = DropDownValue::getList("id", "short_name", ["type" => DropDownValue::COLOR]);

        $sku = $sku_pattern;

        if (isset($record['item_category_id']) && $record['item_category_id'] && isset($item_category_short_list[$record['item_category_id']]))
        {
            $sku = str_replace("{category}", $item_category_short_list[$record['item_category_id']], $sku);
        }
        else
        {
            $sku = str_replace("{category}", "", $sku);
        }

        if (isset($record['brand_id']) && $record['brand_id'] && isset($brand_short_name_list[$record['brand_id']]))
        {
            $sku = str_replace("{brand}", $brand_short_name_list[$record['brand_id']], $sku);
        }
        else
        {
            $sku = str_replace("{brand}", "", $sku);
        }

        if (isset($record['color_id']) && $record['color_id'] && isset($color_short_name_list[$record['color_id']]))
        {
            $sku = str_replace("{color}", $color_short_name_list[$record['color_id']], $sku);
        }
        else
        {
            $sku = str_replace("{color}", "", $sku);
        }

        if (isset($record['size']) && $record['size'])
        {
            $sku = str_replace("{size}", $record['size'], $sku);
        }
        else
        {
            $sku = str_replace("{size}", "", $sku);
        }

        if (isset($record['specification']) && $record['specification'])
        {
            $sku = str_replace("{specification}", $record['specification'], $sku);
        }
        else
        {
            $sku = str_replace("{specification}", "", $sku);
        }

        $sku = preg_replace('/[^a-zA-Z0-9-_.]/', '-', $sku);
        $sku = preg_replace('/-+/', '-', $sku);
        $sku = trim($sku, '-');

        return $sku;
    }
}
