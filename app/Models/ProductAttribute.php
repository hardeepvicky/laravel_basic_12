<?php

namespace App\Models;


class ProductAttribute extends BaseModel
{
    protected $appends = [
        'display_name', 'display_value'
    ];

    protected static array $unique_fields = ["product_id", "product_category_attribute_id"];
    
    public static array $child_model_class = [        
    ];

    public function categoryAttribute()
    {
        return $this->belongsTo(ProductCategoryAttribute::class, "product_category_attribute_id");
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->name;
    }

    protected function getDisplayValueAttribute()
    {
        return $this->getDisplayValue();
    }

    public function getDisplayValue()
    {
        $value = $this->value;
        if ($this->categoryAttribute?->input_type == ProductCategoryAttribute::INPUT_TYPE_DROPDOWN)
        {
            $drop_down_value_list = DropDownValue::getListCache();

            return get_value_from_list($drop_down_value_list, $this->value);
        }
        else if ($this->categoryAttribute?->input_type == ProductCategoryAttribute::INPUT_TYPE_CHECKBOX)
        {
            return (bool) $this->value ? "Yes" : "No";
        }
        else if ($this->categoryAttribute?->input_type == ProductCategoryAttribute::INPUT_TYPE_DATE)
        {
            return if_date($this->value);
        }
        else if ($this->categoryAttribute?->input_type == ProductCategoryAttribute::INPUT_TYPE_DATETIME)
        {
            return if_date_time($this->value);
        }
        else
        {
            if($this->categoryAttribute?->uom_id)
            {
                $uom_list = Uom::getListCache();

                $sub_value = get_value_from_list($uom_list, $this->categoryAttribute?->uom_id);

                $value .= " (" . $sub_value . ")";
            }
        }

        return $value;
    }
}
