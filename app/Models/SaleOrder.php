<?php

namespace App\Models;

use App\Exceptions\UserException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class SaleOrder extends BaseModel
{
    protected $appends = [
        'display_name'
    ];

    protected static array $unique_fields = ["order_no"];

    protected static array $list_fields = ["id", "order_no"];

    public static array $child_model_class = [
        
    ];

    public function afterFindForCsv()
    {
        parent::afterFind();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function carrier()
    {
        return $this->belongsTo(Party::class);
    }

    public function saleOrderProduct()
    {
        return $this->hasMany(SaleOrderProduct::class, 'sale_order_id');
    }

    public function files()
    {
        return $this->belongsToMany(DocumentFile::class, SaleOrderFile::class);
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
            case "branch_id":
                $ret_array = [
                    $field => $this->branch?->getDisplayName()
                ];
                break;
            case "dealer_id":
                $ret_array = [
                    $field => $this->dealer?->getDisplayName()
                ];
                break;
            case "customer_id":
                $ret_array = [
                    $field => $this->customer?->getDisplayName()
                ];
                break;
            case "carrier_id":
                $ret_array = [
                    $field => $this->carrier?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }

    public function getFieldLabels()
    {
        $labels = parent::getFieldLabels();

        $labels['productFile'] = "Files";
        $labels['images'] = "Images";
        $labels['videos'] = "Videos";
        $labels['other_files'] = "Other Files";

        return $labels;
    }

    public function convertToParentUom()
    {
        if ($this->size_uom_id)
        {
            if ($this->length)
            {
                $this->length = Uom::convertToParentUomValue($this->size_uom_id, $this->length);
            }

            if ($this->width)
            {
                $this->width = Uom::convertToParentUomValue($this->size_uom_id, $this->width);
            }

            if ($this->height)
            {
                $this->height = Uom::convertToParentUomValue($this->size_uom_id, $this->height);
            }

            if ($this->gross_length)
            {
                $this->gross_length = Uom::convertToParentUomValue($this->size_uom_id, $this->gross_length);
            }
            if ($this->gross_width)
            {
                $this->gross_width = Uom::convertToParentUomValue($this->size_uom_id, $this->gross_width);
            }
            if ($this->gross_height)
            {
                $this->gross_height = Uom::convertToParentUomValue($this->size_uom_id, $this->gross_height);
            }
        }
    }

    public function convertToChildUom()
    {
        if ($this->size_uom_id)
        {
            if ($this->length)
            {
                $this->length = Uom::convertToChildUomValue($this->size_uom_id, $this->length);
            }

            if ($this->width)
            {
                $this->width = Uom::convertToChildUomValue($this->size_uom_id, $this->width);
            }
            
            if ($this->height)
            {
                $this->height = Uom::convertToChildUomValue($this->size_uom_id, $this->height);
            }

            if ($this->gross_length)
            {
                $this->gross_length = Uom::convertToChildUomValue($this->size_uom_id, $this->gross_length);
            }
            if ($this->gross_width)
            {
                $this->gross_width = Uom::convertToChildUomValue($this->size_uom_id, $this->gross_width);
            }
            if ($this->gross_height)
            {
                $this->gross_height = Uom::convertToChildUomValue($this->size_uom_id, $this->gross_height);
            }
        }
    }

    public static function convertRecordsForChildUom($records)
    {
        $cache_key = "all_uoms";

        $uoms = static::getCache($cache_key);

        if (!$uoms)
        {
            $temp = Uom::get()->toArray();

            foreach($temp as $arr)
            {
                $uoms[$arr['id']] = $arr;
            }

            if ($uoms)
            {                
                Uom::addCache($cache_key, $uoms, 60 * 60 * 24);
            }
        }

        foreach($records as $record)
        {
            if ($record->size_uom_id && isset($uoms[$record->size_uom_id]))
            {
                $record->size_uom_name = $uoms[$record->size_uom_id]['short_name'];
                if ($uoms[$record->size_uom_id]['conversion_factor'])
                {
                    if ($record->length)
                    {
                        $record->length *= $uoms[$record->size_uom_id]['conversion_factor'];
                        $record->length = round($record->length, 3);
                    }

                    if ($record->width)
                    {
                        $record->width *= $uoms[$record->size_uom_id]['conversion_factor'];

                        $record->width = round($record->width, 3);
                    }

                    if ($record->height)
                    {
                        $record->height *= $uoms[$record->size_uom_id]['conversion_factor'];
                        $record->height = round($record->height, 3);
                    }

                    if ($record->gross_length)
                    {
                        $record->gross_length *= $uoms[$record->size_uom_id]['conversion_factor'];
                        $record->gross_length = round($record->gross_length, 3);
                    }

                    if ($record->gross_width)
                    {
                        $record->gross_width *= $uoms[$record->size_uom_id]['conversion_factor'];

                        $record->gross_width = round($record->gross_width, 3);
                    }

                    if ($record->gross_height)
                    {
                        $record->gross_height *= $uoms[$record->size_uom_id]['conversion_factor'];
                        $record->gross_height = round($record->gross_height, 3);
                    }
                }
            }
        }
    }
}
