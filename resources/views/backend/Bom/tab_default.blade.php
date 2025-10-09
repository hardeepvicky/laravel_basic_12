<x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />
<input type="hidden" name="tab_name" value="{{ $model->tab_name }}" />

<div class="row">
    <div class="col-md-12 col-xxl-6">
        <?php
        $field = 'type';
        $label = get_value_from_list($field_labels, $field, $field);
        ?>
        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$type_list"
            :value="$model->{$field}" 
            class="select2" 
            layout="horizontal"
            mandatory="true"
            />


        <div class="item-block">
            <?php
            $field = 'item_id';
            $label = get_value_from_list($field_labels, $field, $field);
            ?>
            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$item_list"
                :value="$model->{$field}" class="select2" layout="horizontal" mandatory="true"/>
        </div>


        <div class="product-block">
            <?php
            $field = 'product_id';
            $label = get_value_from_list($field_labels, $field, $field);
            ?>
            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$product_list"
                :value="$model->{$field}" class="select2" layout="horizontal" mandatory="true"/>
        </div>
    </div>
</div>

<x-Backend.form-common-footer-buttons buttons="next" />
</form>

<script>
    $(function() {
        $("#type").change(function(){
            var v = $(this).val();

            $(".product-block, .item-block").hide();
            $(".product-block, .item-block").find("input,select").attr("disabled", true);

            if (v)
            {
                if (v == "item")
                {
                    $(".item-block").find("input,select").removeAttr("disabled");
                    $(".item-block").show();
                }
                else
                {
                    $(".product-block").find("input,select").removeAttr("disabled");
                    $(".product-block").show();
                }
            }
        }).trigger("change", {pageLoad : true});
        

        $.events.execAtLast(function() {
            
        });
    })
</script>