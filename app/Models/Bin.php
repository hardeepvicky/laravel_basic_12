<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Bin extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected static array $unique_fields = ["branch_id", "name"];

    protected static array $list_fields = ["id", "branch_id", "name", "code"];

    public static array $child_model_class = [
        // User::class => [
        //     "foreignKey" => ["sub_dept_id"],
        //     "preventDelete" => true
        // ]
    ];

    public function afterFindForCsv()
    {
        parent::afterFind();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
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
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")->where("branch_id", $request->input("branch_id"))],
            "code" => [Rule::unique($this->connection . "." . $this->getTable(), "code")],
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
            case "branch_id":
                $ret_array = [
                    $field => $this->branch->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
