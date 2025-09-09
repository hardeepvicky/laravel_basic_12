<?php

namespace App\Models;

use App\Exceptions\UserException;
use App\Helpers\DateUtility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ProductSerial extends BaseModel
{
    protected $appends = [
        'display_name'
    ];

    protected $casts = [
        'manufacture_date_time' => 'datetime:' . DateUtility::DATETIME_OUT_FORMAT,
    ];

    protected static array $unique_fields = ["serial_no"];

    protected static array $list_fields = ["id", "serial_no", "product_id"];

    public static array $child_model_class = [
        ProductSerial::class => [
            "foreignKey" => "parent_id",
            "preventDelete" => true
        ],
    ];
    
    const TYPE_PARENT = "PARENT";
    const TYPE_CHILD = "CHILD";

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

    public static function getAutoNextSerialNo()
    {
        $next_serial_no = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_PRODUCT_SERIAL);
        $next_serial_no = str_pad($next_serial_no, 8, "0", STR_PAD_LEFT);

        return $next_serial_no;
    }

    public static function getManualNextSerialNo()
    {
        $last_record = self::orderBy("id", "DESC")->first();

        $last_serial_no = 0;
        if ($last_record)
        {
            $last_serial_no = (int) $last_record->serial_no;
        }

        $last_serial_no += 1;
        
        $next_serial_no = str_pad($last_serial_no, 8, "0", STR_PAD_LEFT);

        return $next_serial_no;
    }

    public static function getNextSerialNo($current_serial_no)
    {
        $current_serial_no = (int) $current_serial_no;
        $next_serial_no =  $current_serial_no + 1;
        $next_serial_no = str_pad($next_serial_no, 8, "0", STR_PAD_LEFT);

        return $next_serial_no;
    }

    public static function getTypeList()
    {
        return  [
            self::TYPE_CHILD => "Child",
            self::TYPE_PARENT => "Parent"
        ];
    }

    public function markIn(Bin $bin)
    {
        $this->branch_id = $bin->branch_id;
        $this->bin_id = $bin->id;
        $this->is_in = true;

        $this->save();
    }

    public function markInBranch(Branch $branch)
    {
        $this->branch_id = $branch->id;
        $this->bin_id = null;
        $this->is_in = true;

        $this->save();
    }

    public function markOut()
    {
        $this->branch_id = null;
        $this->bin_id = null;
        $this->is_in = false;

        $this->save();
    }
}
