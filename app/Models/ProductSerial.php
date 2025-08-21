<?php

namespace App\Models;

use App\Exceptions\UserException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ProductSerial extends BaseModel
{
    protected $appends = [
        'display_name'
    ];

    protected static array $unique_fields = ["serial_no"];

    protected static array $list_fields = ["id", "serial_no", "product_id"];

    public static array $child_model_class = [
        
    ];

    public function beforeSave()
    {
        parent::beforeSave();
    }
    
    public function afterFind()
    {
        parent::afterFind();
    }

    public function afterFindForCsv()
    {
        parent::afterFind();
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function maker()
    {
        return $this->belongsTo(Maker::class);
    }

    public function dealer()
    {
        return $this->belongsTo(Maker::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer()
    {
        // return $this->belongsTo(Customer::class);
    }

    public function children()
    {
        return $this->hasMany(ProductSerial::class, "parent_id");
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->serial_no;
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "serial_no" => [Rule::unique($this->connection . "." . $this->getTable(), "serial_no")],
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
            case "product_category_id":
                $ret_array = [
                    $field => $this->category?->getDisplayName()
                ];
                break;
                
            case "product_id":
                $ret_array = [
                    $field => $this->product?->getDisplayName()
                ];
                break;
                
            case "make_id":
                $ret_array = [
                    $field => $this->maker?->getDisplayName()
                ];
                break;
                
            case "dealer_id":
                $ret_array = [
                    $field => $this->dealer?->getDisplayName()
                ];
                break;
                
            case "branch_id":
                $ret_array = [
                    $field => $this->branch?->getDisplayName()
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
}
