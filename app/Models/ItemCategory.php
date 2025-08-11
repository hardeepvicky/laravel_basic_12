<?php

namespace App\Models;

use App\Helpers\LaravelExtend;
use App\Helpers\Util;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ItemCategory extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected static array $unique_fields = ["parent_id", "name"];

    public static array $child_model_class = [
        Item::class => [
            "foreignKey" => "item_category_id",
            "preventDelete" => true
        ],       
    ];

    public function beforeSave()
    {
        if (!$this->parent_id)
        {
            $this->parent_id = 0;
        }
        
        if ($this->id)
        {
            if ($this->is_last_node)
            {
                $child_id_list = self::getChildIdList([$this->id]);

                if ($child_id_list)
                {
                    $this->addError("Parent", "This Record has child category, can not make as last node", $this->getClassDisplayName());
                }
            }

            if ($this->id == $this->parent_id)
            {
                $this->addError("Parent", "Category can not be parent of itself", $this->getClassDisplayName());
            }
        }

        return parent::beforeSave();
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, "base_uom_id");
    }

    public function item()
    {
        return $this->hasMany(Product::class);
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
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")->where("parent_id", $request->get("parent_id"))],
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

    public static function getTreeRecords(Builder $qb, $max_depth = 10) : array
    {
        $records = $qb->orderBy("list_display_order")->get();

        $records = Util::getTreeArray($records->toArray(), "parent_id", 0, $max_depth);

        return $records;
    }

    public static function getTreeList(Builder $qb) : array
    {
        return Util::getTreeListArray(self::getTreeRecords($qb, 4), "id", "name", false, false);
    }
}
