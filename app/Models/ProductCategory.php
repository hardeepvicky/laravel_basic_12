<?php

namespace App\Models;

use App\Helpers\LaravelExtend;
use App\Helpers\Util;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ProductCategory extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    public static array $child_model_class = [
        Product::class => [
            "foreignKey" => "product_category_id",
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
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")],
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
