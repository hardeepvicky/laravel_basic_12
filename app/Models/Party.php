<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Party extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected static array $unique_fields = ["type", "name"];

    protected static array $list_fields = ["id", "name", "type"];

    const TYPE_VENDOR = "VENDOR";
    const TYPE_SUPPLIER = "SUPPLIER";
    const TYPE_CONTRACTOR = "CONTRACTOR";

    public static array $child_model_class = [
        // User::class => [
        //     "foreignKey" => ["sub_department_id"],
        //     "preventDelete" => true
        // ]
    ];

    public string $field_for_create_tag = "display_name";

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        $name = $this->name;

        $type_list = $this->getTypeList();

        $name .= " (" . get_value_from_list($type_list, $this->type) . ")";

        return $name;
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "mobile" => [Rule::unique($this->connection . "." . $this->getTable(), "mobile")],
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

    public static function getTypeList()
    {
        $list = [
            self::TYPE_VENDOR => "Vendor",
            self::TYPE_SUPPLIER => "Supplier",
            self::TYPE_CONTRACTOR => "Contractor",
        ];

        return $list;
    }
}
