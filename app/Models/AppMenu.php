<?php

namespace App\Models;

use App\Acl\AccessControl;
use App\Exceptions\UserException;
use App\Helpers\Util;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class AppMenu extends BaseModel
{
    protected static array $unique_fields = [];

    public array $child_model_class = [
        AppMenu::class => [
            "foreignKey" => "parent_id",
            "preventDelete" => true,
        ],
    ];

    protected $appends = [
        'display_name',
    ];

    public function beforeSave()
    {
        if (!$this->parent_id)
        {
            $this->parent_id = 0;
        }

        if ($this->parent_id == $this->id)
        {
            $this->addError("Parent", "Parent and name are same", "Menu");
        }

        return parent::beforeSave();
    }

    public static function classDisplayName(): string
    {
        return "Menu";
    }

    public function getClassDisplayName()
    {
        return "Menu";
    }

    public function parent()
    {
        return $this->belongsTo(AppMenu::class, "parent_id");
    }

    public function child()
    {
        return $this->hasMany(AppMenu::class, "parent_id");
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->name;
    }

    public function getAllChildCount()
    {
        $id_list = $this->getChildIdList([$this->id]);
        
        $this->nested_child_count = count($id_list);
    }


    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
        ];

        if ($this->id) {
            foreach ($field_rules as $field => $rules) {
                foreach ($rules as $rule) {
                    if ($rule instanceof Unique) {
                        $rule->ignore($this->id, $this->primaryKey);
                    }
                }
            }
        }

        return $field_rules;
    }


    public function getRequiedIfRules(Request $request)
    {
        $field_rules = [];

        if ($request->input("is_link", false))
        {
            $field_rules["route_name"] = ["required"];
        }

        return $field_rules;
    }

    public function activate()
    {
        $this->is_active = 1;
        $this->save();

        $accessControl = new AccessControl();
        $accessControl->clearMenuCache();
    }

    public function deActivate()
    {
        $this->is_active = 0;
        $this->save();

        $accessControl = new AccessControl();
        $accessControl->clearMenuCache();
    }

    public static function getTreeRecords(array $conditions = [], $max_depth = 10) : array
    {
        $records = self::where($conditions)->orderBy("menu_display_order")->get();

        $records = Util::getTreeArray($records->toArray(), "parent_id", 0, $max_depth);

        return $records;
    }

    public static function getTreeList(array $conditions = []) : array
    {
        return Util::getTreeListArray(self::getTreeRecords($conditions, 2), "id", "name", false, false);
    }

    public function getChildren()
    {
        $this->load("child");

        if ($this->child->id)
        {
            $this->getChildren();
        }
    }

    public static function getChildIdList(array $parent_id_list)
    {
        $qb = self::query();
        $qb->whereIn("parent_id", $parent_id_list);

        $id_list = $qb->pluck("id")->toArray();

        if ($id_list)
        {
            $id_list = array_merge($id_list, self::getChildIdList($id_list));
        }

        return $id_list;
    }
}
