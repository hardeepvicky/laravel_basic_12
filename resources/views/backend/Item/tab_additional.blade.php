<x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />
<input type="hidden" name="tab_name" value="{{ $model->tab_name }}" />

<div class="row">
    <div class="col-md-10 col-xxl-6">
        <?php
        $field = 'weight';
        $label = get_value_from_list($field_labels, $field, $field);
        ?>
        <div class="row mb-3">
            <div class="col-sm-3">
                <label class="form-label">
                    {{ $label }}
                </label>
            </div>
            <div class="col-sm-9">
                <x-Inputs.group-text-field :id="$field" :name="$field" :label="$false" value="{{ $model->{$field} }}"
                    placeholder="Enter {{ $label }}"
                    class="validate-float"
                    layout="horizontal"
                    secondaryTextPosition="last"
                    secondaryText='<span class="input-group-text">kg.</span>'
                    />
            </div>
        </div>

        <?php
        $field = 'minimum_order_qty';
        $label = get_value_from_list($field_labels, $field, $field);
        ?>
        <x-Inputs.text-field :id="$field" :name="$field" :label="$label" value="{{ $model->{$field} }}"
            placeholder="Enter {{ $label }}"
            class="validate-float"
            layout="horizontal" />

        <?php
        $field = 'origin_id';
        $label = get_value_from_list($field_labels, $field, $field);
        ?>
        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$origin_list"
            :value="$model->{$field}"
            :pleaseSelect="$false"
            class="select2"
            layout="horizontal"
            />

        <?php
        $field = 'certification_id';
        $label = get_value_from_list($field_labels, $field, $field);
        ?>
        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$certification_list"
            :value="$model->{$field}"
            :pleaseSelect="$false"
            class="select2"
            layout="horizontal"
            />

        <?php
        $field = 'other_files';
        $label = get_value_from_list($field_labels, $field, $field);
        $value = implode(',', $model->otherFiles?->pluck('id')->toArray());
        ?>
        <div class="row mb-3">
            <div class="col-sm-3">
                <label class="form-label">
                    {{ $label }}
                </label>
            </div>
            <div class="col-sm-9">
                <span id="choose-other_files" class="btn btn-secondary">Choose Files</span>
                <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                    :value="$value" multiple="multiple" />
            </div>
        </div>

        <?php
        $field = 'reference';
        $label = get_value_from_list($field_labels, $field, $field);
        ?>
        <x-Inputs.text-field :name="$field" :label="$label"
            value="{{ $model->{$field} }}" placeholder="Enter {{ $label }}"
            layout="horizontal" />

        <?php
        $field = 'tag_name';
        $label = get_value_from_list($field_labels, $field, 'Tags');
        $value = implode(',', $model->tags?->pluck('name')->toArray()) ?? [];
        ?>
        <x-Inputs.choice :id="$field" :name="$field" :label="$label" :value="$value"
            class="choice" layout="horizontal"  placeholder="Enter {{ $label }}" />

        <?php
        $field = 'description';
        $label = get_value_from_list($field_labels, $field, $field);
        ?>
        <x-Inputs.text-area :id="$field" :name="$field" :label="$label" rows="3"
            value="{{ $model->{$field} }}" placeholder="Enter {{ $label }}" layout="horizontal" />

        <?php
        $field = 'list_display_order';
        $label = get_value_from_list($field_labels, $field, $field);
        ?>
        <x-Inputs.text-field type="number" :name="$field" :label="$label"
            value="{{ $model->{$field} }}" placeholder="Enter {{ $label }}"
            mandatory="true" layout="horizontal"  autocomplete="off" />

        <div class="row">
            <div class="col-sm-3"></div>
            <div class="col-sm-9">
                <?php
                $field = 'can_be_use_for_bom';
                $label = get_value_from_list($field_labels, $field, $field);
                ?>
                <x-Inputs.checkbox :name="$field" :label="$label"
                    value="{{ $model->{$field} }}" />
                    
                <?php
                $field = 'is_active';
                $label = get_value_from_list($field_labels, $field, $field);
                ?>
                <x-Inputs.checkbox :name="$field" :label="$label"
                    value="{{ $model->{$field} }}" />
            </div>
        </div>
    </div>

    <div class="col-md-2 col-xxl-6">
        <span class="btn btn-secondary" id="modal-copy-detail-from-other-item-opener">Copy Details From Other Item</span>

        <div class="modal fade" id="modal-copy-detail-from-other-item" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title fs-5">Choose Item</h3>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <?php
                        $field = 'item_id';
                        $label = "Item";
                        ?>
                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                            :list="$item_list"                            
                            layout="horizontal"
                            />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary submit">Done</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-Backend.form-common-footer-buttons buttons="next" />
</form>

<script>
    $(function() {
        var screen_name = '<?= $screen_name ?>';
        $("#other_files").browseGallery({
            opener: "#choose-other_files",
            postfix: "choose-other_files",
            index_table_id : "document_file_others"
        });

        $("#modal-copy-detail-from-other-item-opener").click(function(){
            $("#modal-copy-detail-from-other-item").modal("show");
        });

        $("#modal-copy-detail-from-other-item .submit").click(function()
        {
            var v = $("#item_id").val();
            if (v)
            {
                ajaxGetJson("/item/ajax_get/" + v , function(response){
                    console.log(response);

                    var data = response['data'];

                    $("#weight").val(data['weight']);
                    $("#minimum_order_qty").val(data['minimum_order_qty']);
                    $("#description").val(data['description']);
                    
                    $("#origin_id").val(data['origin_id']).trigger("change", {pageLoad : true});
                    $("#certification_id").val(data['certification_id']).trigger("change", {pageLoad : true});
                }); 
                
                $("#modal-copy-detail-from-other-item").modal("hide");
            }
        });
    })
</script>