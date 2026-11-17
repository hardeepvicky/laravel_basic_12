<?php

namespace App\Models;

use App\Exceptions\UserException;
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
        ProductCategoryAttribute::class => [
            "foreignKey" => "uom_id",
            "preventDelete" => true
        ],
    ];

    public function parent()
    {
        return $this->belongsTo(Uom::class, "parent_id");
    }

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
        return Util::getTreeListArray(self::getTreeRecords($qb, 1), "id", "name", false, false);
    }

    public static function getUomsForCode($code)
    {
        $parent = static::where("code", $code)->first();

        $children = [];

        if ($parent)
        {
            $children = static::where("parent_id", $parent->id)->orderBy("list_display_order", "ASC")->get();
        }

        return compact("parent", "children");
    }

    public static function getUomListForCode($code)
    {
        $data = static::getUomsForCode($code);

        $list = [];

        if (isset($data['parent']) && $data['parent'])
        {
            $list[$data['parent']->id] = $data['parent']->getDisplayName();
        }

        if (isset($data['children']) && $data['children'])
        {
            foreach($data['children'] as $child)
            {
                $list[$child->id] = $child->getDisplayName();
            }
        }

        return $list;
    }

    public static function convertToParentUomValue($uom_id, $value)
    {
        $cache_key = "uom_" . $uom_id;

        $uom = static::getCache($cache_key);

        if (!$uom)
        {
            $uom = static::find($uom_id);

            if (!$uom)
            {
                throw new UserException("Wrong uom_id : " . $uom_id);
            }

            static::addCache($cache_key, $uom, 60 * 60 * 24);
        }

        if ($uom->conversion_factor && $uom->conversion_factor != 0 && is_numeric($value))
        {
            $value /= $uom->conversion_factor;

            $value = round($value, 3);
        }

        return $value;
    }

    public static function convertToChildUomValue($uom_id, $value)
    {
        $cache_key = "uom_" . $uom_id;

        $uom = static::getCache($cache_key);

        if (!$uom)
        {
            $uom = static::find($uom_id);

            if (!$uom)
            {
                throw new UserException("Wrong uom_id : " . $uom_id);
            }

            static::addCache($cache_key, $uom, 60 * 60 * 24);
        }

        if ($uom->conversion_factor && $uom->conversion_factor != 0 && is_numeric($value))
        {
            $value *= $uom->conversion_factor;

            $value = round($value, 3);
        }

        return $value;
    }
}
