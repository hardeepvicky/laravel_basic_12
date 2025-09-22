<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Branch extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    public static array $child_model_class = [
        StockProduct::class => [
            "foreignKey" => ["branch_id"],
            "preventDelete" => true
        ],
        User::class => [
            "foreignKey" => ["branch_id"],
            "preventDelete" => true
        ],
        Employee::class => [
            "foreignKey" => ["branch_id"],
            "preventDelete" => true
        ],
        Bin::class => [
            "foreignKey" => ["branch_id"],
            "preventDelete" => true
        ],
        SaleOrder::class => [
            "foreignKey" => ["branch_id"],
            "preventDelete" => true
        ],
        ExportOrder::class => [
            "foreignKey" => ["branch_id"],
            "preventDelete" => true
        ],
        Requirement::class => [
            "foreignKey" => ["branch_id"],
            "preventDelete" => true
        ],
        PurchaseOrder::class => [
            "foreignKey" => ["branch_id"],
            "preventDelete" => true
        ],
    ];

    protected static array $unique_fields = ["company_id", "name"];

    protected static array $list_fields = ["id", "name"];

    protected static array $fields_containg_tags = ["description"];

    public string $field_for_create_tag = "display_name";

    const TYPE_HEAD_OFFCE = "HEAD_OFFCE";
    const TYPE_REGIONAL_OFFCE = "REGIONAL_OFFCE";
    const TYPE_MANUFACTURE_UNIT = "MANUFACTURE_UNIT";
    const TYPE_SALE_UNIT = "SALE_UNIT";
    const TYPE_WAREHOUSE = "WAREHOUSE";

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        $name = $this->name;

        return $name;
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->parseTagsForModuleLinking();
    }

    public function afterFindForCsv()
    {
        parent::afterFind();
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
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

    public static function getCodeList(String $field)
    {
        $class_info = get_class_info(static::class);


        switch ($field) {
            case "type":

                $list = [];

                foreach ($class_info['constants'] as $constant => $label) {
                    if (strpos($constant, "TYPE_") !== false) {
                        $list[] = $label;
                    }
                }

                return $list;

                break;
        }
    }
}
