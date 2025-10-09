<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Bom extends BaseModel
{
    protected static array $unique_fields = ["item_id", "product_id"];

    const TYPE_ITEM = "item";
    const TYPE_PRODUCT = "product";

    public static array $child_model_class = [
        BomItem::class => [
            "foreignKey" => ["bom_id"],
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

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function parentItemCategory()
    {
        return $this->belongsTo(ItemCategory::class, "parent_item_category_id");
    }

    public function bomItems()
    {
        return $this->hasMany(BomItem::class);
    }

    public function getDisplayName()
    {
        return $this->item?->getDisplayName() ?? "";
    }

    public function getUniqueValidationRules(Request $request)
    {
        if ($request->type == "item")
        {
            $field_rules = [
                "item_id" => [Rule::unique($this->connection . "." . $this->getTable(), "item_id")],
            ];
        }
        else
        {
            $field_rules = [
                "product_id" => [Rule::unique($this->connection . "." . $this->getTable(), "product_id")],
            ];
        }
        

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
            case "item_id":
                $ret_array = [
                    $field => $this->item?->getDisplayName()
                ];
                break;
            case "product_id":
                $ret_array = [
                    $field => $this->product?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }

    public static function getTypeList(){
        return [
            self::TYPE_ITEM => "Item",
            self::TYPE_PRODUCT => "Product",
        ];
    }

}