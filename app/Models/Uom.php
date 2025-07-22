<?php

namespace App\Models;

use App\Helpers\Util;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Uom extends BaseModel
{
    protected $appends = [
        'display_name', "name_short_name"
    ];

    protected static array $unique_fields = ["parent_id", "name"];

    protected static array $list_fields = ["id", "name", "short_name"];

    public static array $child_model_class = [
        Product::class => [
            "foreignKey" => "uom_id",
            "preventDelete" => true
        ],
        ProductCategoryAttribute::class => [
            "foreignKey" => "uom_id",
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
                    $this->addError("Parent", "This Record has child UOM, can not make as last node", $this->getClassDisplayName());
                }
            }

            if ($this->id == $this->parent_id)
            {
                $this->addError("Parent", "UOM can not be parent of itself", $this->getClassDisplayName());
            }
        }

        return parent::beforeSave();
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->short_name;
    }

    protected function getNameShortNameAttribute()
    {
        return $this->getNameShortName();
    }

    public function getNameShortName()
    {
        return $this->name . " (" . $this->short_name . ")";
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

    public function getRequiedIfRules(Request $request)
    {
        $rules = [];

        if ($request->input("parent_id", false)) {
            $rules["conversion_factor"] = ["required"];
            $rules["short_name"] = ["required"];
        }

        return $rules;
    }

    public static function getTreeRecords(Builder $qb, $max_depth = 10) : array
    {
        $records = $qb->orderBy("list_display_order")->get();

        $records = Util::getTreeArray($records->toArray(), "parent_id", 0, $max_depth);

        return $records;
    }

    public static function getTreeList(Builder $qb) : array
    {
        return Util::getTreeListArray(self::getTreeRecords($qb, 2), "id", "name", false, false);
    }
}
