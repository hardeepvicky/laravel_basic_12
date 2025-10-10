<?php

namespace App\Models;

use App\Exceptions\UserException;
use App\Helpers\DateUtility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ItemSerial extends BaseModel
{
    protected $appends = [
        'display_name'
    ];

    protected $casts = [
    ];

    protected static array $unique_fields = ["serial_no"];

    protected static array $list_fields = ["id", "serial_no", "item_id"];

    public static array $child_model_class = [
        
    ];
    
    public function beforeDelete()
    {
        $qty = -1 * $this->package_qty;

        StockItem::changeQty($this->branch_id, $this->bin_id, $this->item_id, null, $qty);
    }

    public function afterFindForCsv()
    {
        parent::afterFind();
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function bin()
    {
        return $this->belongsTo(Bin::class);
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
            case "item_id":
                $ret_array = [
                    $field => $this->item?->getDisplayName()
                ];
                break;
             
            case "branch_id":
                $ret_array = [
                    $field => $this->branch?->getDisplayName()
                ];
                break;

            case "bin_id":
                $ret_array = [
                    $field => $this->bin?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }

    public static function getAutoNextSerialNo()
    {
        $next_serial_no = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_ITEM_SERIAL);
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
