<?php

namespace App\Models;

use App\Exceptions\UserException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ProductIssue extends BaseModel
{
    protected $appends = [
        'display_name'
    ];

    protected static array $unique_fields = ["name"];

    protected static array $list_fields = ["id", "name"];

    public static array $child_model_class = [
        ProductIssueSolution::class => [
            "foreignKey" => ["issue_id"],
            "preventDelete" => false
        ],
        ServiceIssue::class => [
            "foreignKey" => ["issue_id"],
            "preventDelete" => true
        ],
    ];

    public function afterFindForCsv()
    {
        parent::afterFind();
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        $name = $this->name;

        return $name;
    }

    public function issueSolution()
    {
        return $this->hasMany(ProductIssueSolution::class, "issue_id");
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")->where("product_category_id", $request->get("product_category_id"))],
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
}
