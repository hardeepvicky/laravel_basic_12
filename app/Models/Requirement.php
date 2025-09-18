<?php

namespace App\Models;

use App\Helpers\DateUtility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Requirement extends BaseModel
{
    protected $appends = [
        'display_name'
    ];

    protected $casts = [
    ];

    protected static array $unique_fields = [];
    
    const STATUS_PENDING = "PENDING";
    const STATUS_PO_CREATED = "PO_CREATED";
    const STATUS_FULLFILLED = "FULLFILLED";
    
    public static array $child_model_class = [
        // SaleOrderProduct::class => [
        //     "foreignKey" => "sale_order_id",
        //     "preventDelete" => false
        // ],
    ];

    public function afterFindForCsv()
    {
        parent::afterFind();
    }

    
    public function branch()
    {
        return $this->belongsTo(Branch::class, "branch_id");
    }
    
    public function department()
    {
        return $this->belongsTo(Department::class, "dept_id");
    }

    public function subDepartment()
    {
        return $this->belongsTo(SubDepartment::class, "sub_dept_id");
    }

    public function item()
    {
        return $this->belongsTo(Item::class, "item_id");
    }

    public function make()
    {
        return $this->belongsTo(Maker::class, "make_id");
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, "uom_id");
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->order_no;
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "order_no" => [Rule::unique($this->connection . "." . $this->getTable(), "order_no")],
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
            case "dept_id":
                $ret_array = [
                    $field => $this->department?->getDisplayName()
                ];
                break;
            case "sub_dept_id":
                $ret_array = [
                    $field => $this->subDepartment?->getDisplayName()
                ];
                break;
            case "item_id":
                $ret_array = [
                    $field => $this->item?->getDisplayName()
                ];
                break;
            case "make_id":
                $ret_array = [
                    $field => $this->make?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }

    public function getFieldLabels()
    {
        $labels = parent::getFieldLabels();

        return $labels;
    }

    public static function getStatusTypeList()
    {
        return [
            self::STATUS_PENDING => "Pending",
            self::STATUS_PO_CREATED => "Purchase Order Created",
            self::STATUS_FULLFILLED => "Fullfilled",
        ];
    }
}
