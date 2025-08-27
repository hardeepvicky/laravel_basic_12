<x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />
<input type="hidden" name="tab_name" value="{{ $model->tab_name }}" />

<div class="row">
    <div class="col-md-12 col-xxl-6">
            <?php
            $field = 'product_category_id';
            $label = get_value_from_list($field_labels, $field, $field);
            ?>
            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$product_category_list"
                :value="$model->{$field}" 
                layout="horizontal"
                class="select2"
                mandatory="true" />

            <?php
            $field = 'name';
            $label = get_value_from_list($field_labels, $field, $field);
            ?>
            <x-Inputs.text-field :name="$field" :label="$label" 
                :value="$model->{$field}"
                layout="horizontal"
                placeholder="Enter {{ $label }}" 
                mandatory="true" />

            <?php
            $field = 'model_name';
            $label = get_value_from_list($field_labels, $field, $field);
            ?>
            <x-Inputs.text-field :name="$field" :label="$label" 
                :value="$model->{$field}"
                layout="horizontal"
                placeholder="Enter {{ $label }}" 
                mandatory="true" />

            <?php
            $field = 'sku';
            $label = get_value_from_list($field_labels, $field, $field);
            ?>
            <x-Inputs.text-field :name="$field" :label="$label" 
                :value="$model->{$field}"
                layout="horizontal"
                placeholder="Enter {{ $label }}" 
                mandatory="true" />

            <div class="d-flex align-items-center">
                <div class="b-r-dark p-1">
                    <div class="d-flex align-items-start">
                        <div class="form-group w-50 mb-3">
                            <?php
                            $field = 'length';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                class="validate-float" placeholder="Enter {{ $label }}" />
                        </div>
                        <div class="form-group w-50 mb-3">
                            <?php
                            $field = 'width';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                class="validate-float" placeholder="Enter {{ $label }}" />
                        </div>
                        <div class="form-group w-50 mb-3">
                            <?php
                            $field = 'height';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                class="validate-float" placeholder="Enter {{ $label }}" />
                        </div>
                    </div>
                    <div class="d-flex align-items-start">
                        <div class="form-group w-50 mb-3">
                            <?php
                            $field = 'gross_length';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                class="validate-float" placeholder="Enter {{ $label }}" />
                        </div>
                        <div class="form-group w-50 mb-3">
                            <?php
                            $field = 'gross_width';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                class="validate-float" placeholder="Enter {{ $label }}" />
                        </div>
                        <div class="form-group w-50 mb-3">
                            <?php
                            $field = 'gross_height';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                class="validate-float" placeholder="Enter {{ $label }}" />
                        </div>
                    </div>
                </div>
                <div class="form-group flex-shrink-1 mb-3 p-1">
                    <?php
                    $field = 'size_uom_id';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$uom_list"
                        :value="$model->{$field}" class="select2" />
                </div>
            </div>

            <?php
            $field = 'net_weight';
            $label = get_value_from_list($field_labels, $field, $field);
            ?>
            <x-Inputs.group-text-field :name="$field" :label="$label" 
                :value="$model->{$field}"
                layout="horizontal"
                class="validate-float" 
                placeholder="Enter {{ $label }}"
                secondaryText='<span class="input-group-text">kg.</span>' 
                secondaryTextPosition="last"
                mandatory="true" />

            <?php
            $field = 'gross_weight';
            $label = get_value_from_list($field_labels, $field, $field);
            ?>
            <x-Inputs.group-text-field :name="$field" :label="$label" 
                :value="$model->{$field}"
                class="validate-float" 
                layout="horizontal"
                placeholder="Enter {{ $label }}"
                secondaryText='<span class="input-group-text">kg.</span>'
                secondaryTextPosition="last"
                mandatory="true" 
            />

            <?php
            $field = 'images';
            $label = get_value_from_list($field_labels, $field, $field);
            $value = implode(',', $model->images?->pluck('id')->toArray());
            ?>
            <div class="row mb-3">
                <div class="col-sm-3">
                    <label class="form-label">
                        {{ $label }}
                    </label>
                </div>
                <div class="col-sm-9">
                    <span id="choose-images" class="btn btn-secondary">Choose Images</span>
                    <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$value"
                        multiple="multiple" />
                </div>
            </div>

            <?php
            $field = 'videos';
            $label = get_value_from_list($field_labels, $field, $field);
            $value = implode(',', $model->videos?->pluck('id')->toArray());
            ?>
            <div class="row mb-3">
                <div class="col-sm-3">
                    <label class="form-label">
                        {{ $label }}
                    </label>
                </div>
                <div class="col-sm-9">
                    <span id="choose-videos" class="btn btn-secondary">Choose Videos</span>
                    <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$value"
                        multiple="multiple" />
                </div>
            </div>

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
                    <span id="choose-other_files" class="btn btn-secondary">Choose Documents</span>
                    <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$value"
                        multiple="multiple" />
                </div>
            </div>

            <?php
            $field = 'catalog_document_file_id';
            $label = get_value_from_list($field_labels, $field, $field);
            $value = $model->catalog_document_file_id;
            ?>
            <div class="row mb-3">
                <div class="col-sm-3">
                    <label class="form-label">
                        {{ $label }}
                    </label>
                </div>
                <div class="col-sm-9">
                    <span id="choose-catalog_document_file_id" class="btn btn-secondary">Choose Catalog</span>
                    <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$value" />
                </div>
            </div>

            <?php
            $field = 'installation_guide_document_file_id';
            $label = get_value_from_list($field_labels, $field, $field);
            $value = $model->catalog_document_file_id;
            ?>
            <div class="row mb-3">
                <div class="col-sm-3">
                    <label class="form-label">
                        {{ $label }}
                    </label>
                </div>
                <div class="col-sm-9">
                    <span id="choose-installation_guide_document_file_id" class="btn btn-secondary">Choose Installation Guide</span>
                    <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$value" />
                </div>
            </div>

            <?php
            $field = 'user_manual_document_file_id';
            $label = get_value_from_list($field_labels, $field, $field);
            $value = $model->catalog_document_file_id;
            ?>
            <div class="row mb-3">
                <div class="col-sm-3">
                    <label class="form-label">
                        {{ $label }}
                    </label>
                </div>
                <div class="col-sm-9">
                    <span id="choose-user_manual_document_file_id" class="btn btn-secondary">Choose User Manual Guide</span>
                    <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$value" />
                </div>
            </div>

            <?php
            $field = 'troubleshoot_document_file_id';
            $label = get_value_from_list($field_labels, $field, $field);
            $value = $model->catalog_document_file_id;
            ?>
            <div class="row mb-3">
                <div class="col-sm-3">
                    <label class="form-label">
                        {{ $label }}
                    </label>
                </div>
                <div class="col-sm-9">
                    <span id="choose-troubleshoot_document_file_id" class="btn btn-secondary">Choose Troubleshoot Guide</span>
                    <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$value" />
                </div>
            </div>
        </div>
        <?php
        $field = 'list_display_order';
        $label = get_value_from_list($field_labels, $field, $field);
        ?>
        <x-Inputs.text-field type="number" :name="$field" :label="$label"
            :value="$model->{$field}" 
            layout="horizontal"
            placeholder="Enter {{ $label }}" 
            mandatory="true"
            autocomplete="off" />

        <?php
        $field = 'is_active';
        $label = get_value_from_list($field_labels, $field, $field);
        ?>
        <x-Inputs.checkbox :name="$field" :label="$label" :value="$model->{$field}" />
    </div>
</div>

<x-Backend.form-common-footer-buttons buttons="next" />
</form>


<script>
    var id = '<?= $model->id ?? 0 ?>';
    $(function() {
        $("#product_category_id").change(function() {
            var v = $(this).val();

            $("#load_field_block").html("");

            if (v) {
                $.loader.init().show();
                $("#load_field_block").load("/product/ajax_load_attribute_fields/" + v + "/" + id,
                    function() {
                        $.loader.hide();
                    })
            }
        });

        $("#images").browseGallery({
            opener: "#choose-images",
            postfix: "choose-images",
            type: "image",
            index_table_id: "document_file_images"
        });

        $("#videos").browseGallery({
            opener: "#choose-videos",
            postfix: "choose-videos",
            type: "video",
            index_table_id: "document_file_videos"
        });
        $("#other_files").browseGallery({
            opener: "#choose-other_files",
            postfix: "choose-other_files",
            index_table_id: "document_file_others"
        });
        $("#catalog_document_file_id").browseGallery({
            opener: "#choose-catalog_document_file_id",
            postfix: "choose-catalog_document_file_id",
            type: "pdf",
            index_table_id: "document_catalog_document_file_id"
        });
        $("#installation_guide_document_file_id").browseGallery({
            opener: "#choose-installation_guide_document_file_id",
            postfix: "choose-installation_guide_document_file_id",
            type: "pdf",
            index_table_id: "installation_guide_document_file_id"
        });
        $("#user_manual_document_file_id").browseGallery({
            opener: "#choose-user_manual_document_file_id",
            postfix: "choose-user_manual_document_file_id",
            type: "pdf",
            index_table_id: "user_manual_document_file_id"
        });
        $("#troubleshoot_document_file_id").browseGallery({
            opener: "#choose-troubleshoot_document_file_id",
            postfix: "choose-troubleshoot_document_file_id",
            type: "pdf",
            index_table_id: "troubleshoot_document_file_id"
        });

        $.events.execAtLast(function() {

            $("#product_category_id").trigger("sr-cascade.change", {
                pageLoad: true
            });
        });
    });
</script>
