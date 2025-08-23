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
        return $this->belongsTo(Maker::class, "make_id");
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

        $labels['type'] = "Type";
        $labels['no_of_serials'] = "No. Of Serials to generate";
        $labels['is_parent_serial_no'] = "Is Parent Serial No.";

        return $labels;
    }

    public static function getNextSerialNo()
    {
        $next_serial_no = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_PRODUCT);
        $next_serial_no = str_pad($next_serial_no, 8, "0", STR_PAD_LEFT);

        return $next_serial_no;
    }

    public static function getManualNextSerialNo($current_serial_no)
    {
        $current_serial_no = (int) $current_serial_no;
        $next_serial_no =  $current_serial_no + 1;
        $next_serial_no = str_pad($next_serial_no, 8, "0", STR_PAD_LEFT);

        return $next_serial_no;
    }
}
