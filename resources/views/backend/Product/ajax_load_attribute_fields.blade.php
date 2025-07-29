<?php
use App\Models\ProductCategoryAttribute;
?>
<div class="b-info p-1 mb-2">
    <h3 class="b-b-info">
        Custom Attributes
    </h3>
    @foreach($category->categoryAttribute as $categoryAttribute)
    <?php
        $k = $categoryAttribute->id;
    ?>
    <div class="form-group mb-2">
        @if($categoryAttribute->input_type == ProductCategoryAttribute::INPUT_TYPE_DROPDOWN)
            <x-Inputs.drop-down name="product_attributes[{{ $k }}]" :label="$categoryAttribute->name" :list="$drop_down_lists[$categoryAttribute->id]" :value="$categoryAttribute->value" class="select2"/>
        @elseif($categoryAttribute->input_type == ProductCategoryAttribute::INPUT_TYPE_CHECKBOX) 
            <x-Inputs.checkbox name="product_attributes[{{ $k }}]" :label="$categoryAttribute->name" :value="$categoryAttribute->value"/>
        @else
            <?php 
            $css_class = "";
            if($categoryAttribute->input_type == ProductCategoryAttribute::INPUT_TYPE_INT)
            {
                $css_class = "validate-int";
            }
            else if($categoryAttribute->input_type == ProductCategoryAttribute::INPUT_TYPE_DECIMAL)
            {
                $css_class = "validate-float";
            }
            else if($categoryAttribute->input_type == ProductCategoryAttribute::INPUT_TYPE_DATE)
            {
                $css_class = "date-picker";
            }
            else if($categoryAttribute->input_type == ProductCategoryAttribute::INPUT_TYPE_DATETIME)
            {
                $css_class = "date-time-picker";
            }
            ?>
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">                
                    <x-Inputs.text-field name="product_attributes[{{ $k }}]" :label="$categoryAttribute->name"  :class="$css_class" :value="$categoryAttribute->value"/>
                </div>                
                @if(in_array($categoryAttribute->input_type, [ProductCategoryAttribute::INPUT_TYPE_INT, ProductCategoryAttribute::INPUT_TYPE_DECIMAL, ProductCategoryAttribute::INPUT_TYPE_TEXT]))
                    <div class="flex-shrink-0 border p-1" style="margin-top: 30px;">
                        {{ get_value_from_list($uom_list, $categoryAttribute->uom_id) }}
                    </div>
                @endif
            </div>
        @endif
    </div>
    @endforeach
</div>