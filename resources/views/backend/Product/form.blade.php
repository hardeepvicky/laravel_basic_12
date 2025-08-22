@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />

    <div class="row">
        <div class="col-xxxl-12">
            <div class="card">
                <div class="card-header card-no-border pb-0">
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 col-xxl-6">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'product_category_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$product_category_list"
                                    value="{{ $model->{$field} }}" class="select2" 
                                    mandatory="true"
                                    />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" mandatory="true" />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'model_name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" mandatory="true" />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'sku';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" mandatory="true" />
                            </div>
                            <div class="row">
                                <div class="d-flex align-items-center">
                                    <div class="b-r-dark p-1">
                                        <div class="d-flex align-items-start">
                                            <div class="form-group w-50 mb-3">
                                                <?php
                                                $field = 'length';
                                                $label = get_value_from_list($field_labels, $field, $field);
                                                ?>
                                                <x-Inputs.text-field :name="$field" :label="$label"
                                                    value="{{ $model->{$field} }}" class="validate-float"
                                                    placeholder="Enter {{ $label }}"
                                                    mandatory="true" />
                                            </div>
                                            <div class="form-group w-50 mb-3">
                                                <?php
                                                $field = 'width';
                                                $label = get_value_from_list($field_labels, $field, $field);
                                                ?>
                                                <x-Inputs.text-field :name="$field" :label="$label"
                                                    value="{{ $model->{$field} }}" class="validate-float"
                                                    placeholder="Enter {{ $label }}"
                                                    mandatory="true"
                                                    />
                                            </div>
                                            <div class="form-group w-50 mb-3">
                                                <?php
                                                $field = 'height';
                                                $label = get_value_from_list($field_labels, $field, $field);
                                                ?>
                                                <x-Inputs.text-field :name="$field" :label="$label"
                                                    value="{{ $model->{$field} }}" class="validate-float"
                                                    placeholder="Enter {{ $label }}"
                                                    mandatory="true" />
                                            </div>                                    
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <div class="form-group w-50 mb-3">
                                                <?php
                                                $field = 'gross_length';
                                                $label = get_value_from_list($field_labels, $field, $field);
                                                ?>
                                                <x-Inputs.text-field :name="$field" :label="$label"
                                                    value="{{ $model->{$field} }}" class="validate-float"
                                                    placeholder="Enter {{ $label }}"
                                                    mandatory="true" />
                                            </div>
                                            <div class="form-group w-50 mb-3">
                                                <?php
                                                $field = 'gross_width';
                                                $label = get_value_from_list($field_labels, $field, $field);
                                                ?>
                                                <x-Inputs.text-field :name="$field" :label="$label"
                                                    value="{{ $model->{$field} }}" class="validate-float"
                                                    placeholder="Enter {{ $label }}"
                                                    mandatory="true"
                                                    />
                                            </div>
                                            <div class="form-group w-50 mb-3">
                                                <?php
                                                $field = 'gross_height';
                                                $label = get_value_from_list($field_labels, $field, $field);
                                                ?>
                                                <x-Inputs.text-field :name="$field" :label="$label"
                                                    value="{{ $model->{$field} }}" class="validate-float"
                                                    placeholder="Enter {{ $label }}"
                                                    mandatory="true" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group w-100 mb-3 p-1">
                                        <?php
                                        $field = 'size_uom_id';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$uom_list"
                                            value="{{ $model->{$field} }}" class="select2" mandatory="true" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'net_weight';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.group-text-field :name="$field" :label="$label"
                                    value="{{ $model->{$field} }}" class="validate-float"
                                    placeholder="Enter {{ $label }}"
                                    secondaryText='<span class="input-group-text">kg.</span>'
                                    secondaryTextPosition="last" mandatory="true" />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'gross_weight';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.group-text-field :name="$field" :label="$label"
                                    value="{{ $model->{$field} }}" class="validate-float"
                                    placeholder="Enter {{ $label }}"
                                    secondaryText='<span class="input-group-text">kg.</span>'
                                    secondaryTextPosition="last" mandatory="true" />
                            </div>
                            <div id="load_field_block">
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'images';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        $value = implode(',', $model->images?->pluck('id')->toArray());
                                        ?>
                                        <span id="choose-images" class="btn btn-secondary">Choose Images</span>
                                        <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                                            :value="$value" multiple="multiple" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'videos';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        $value = implode(',', $model->videos?->pluck('id')->toArray());
                                        ?>
                                        <span id="choose-videos" class="btn btn-secondary">Choose Videos</span>
                                        <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                                            :value="$value" multiple="multiple" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'other_files';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        $value = implode(',', $model->otherFiles?->pluck('id')->toArray());
                                        ?>
                                        <span id="choose-other_files" class="btn btn-secondary">Choose Documents</span>
                                        <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                                            :value="$value" multiple="multiple" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = "catalog_document_file_id";
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        $value = $model->catalog_document_file_id;
                                        ?>
                                        <span id="choose-catalog_document_file_id" class="btn btn-secondary">Choose Catalog</span>
                                        <x-Inputs.text-field :id="$field" :name="$field" :label="$label" :value="$value" />                        
                                    </div>
                                </div>
                            </div>     
                            <div class="form-group mb-3">
                                <?php
                                $field = 'seo_title';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'meta_main_keyword';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'meta_description';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'keywords';
                                $label = get_value_from_list($field_labels, $field, 'Keywords');
                                $value = implode(',', $model->keywords?->pluck('name')->toArray()) ?? [];
                                ?>
                                <x-Inputs.choice :id="$field" :name="$field" :label="$label" :value="$value"
                                    class="choice " placeholder="Enter {{ $label }}" />
                            </div>
                            <div class="form-group mb-3">
                                <div class="textarea-wrapper">
                                    <?php
                                    $field = 'description';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.text-area :id="$field" :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                        class="auto-suggesstion-list"
                                        rows="4"
                                        placeholder="Enter {{ $label }}"
                                        />
                                    <div id="{{ $field . '-suggestion-box' }}" class="suggestion-box hidden"></div>
                                </div>

                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'description_html';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-area :id="$field" :name="$field" :label="$label"
                                    :value="$model->{$field}" />
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'list_display_order';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.text-field type="number" :name="$field" :label="$label"
                                            value="{{ $model->{$field} }}" placeholder="Enter {{ $label }}"
                                            mandatory="true" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-xl-6" style="padding-top: 30px">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'is_active';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.checkbox :name="$field" :label="$label"
                                            value="{{ $model->{$field} }}" />
                                    </div>
                                </div>
                            </div>                       
                        </div>
                    </div>
                </div>
            </div>

            <x-Backend.form-common-footer-buttons />
        </div>
    </div>
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
                index_table_id : "document_file_images"
            });

            $("#videos").browseGallery({
                opener: "#choose-videos",
                postfix: "choose-videos",
                type: "video",
                index_table_id : "document_file_videos"
            });
            $("#other_files").browseGallery({
                opener: "#choose-other_files",
                postfix: "choose-other_files",
                index_table_id : "document_file_others"
            });
            $("#catalog_document_file_id").browseGallery({
                opener: "#choose-catalog_document_file_id",
                postfix: "choose-catalog_document_file_id",
                type: "pdf",
                index_table_id : "document_catalog_document_file_id"
            });

            suggestion_list($("#description"), $("#description-suggestion-box"), [], "/tags/ajax_get_list");

            $("#description_html").summernote({
                height: 600,
            });

            $.events.execAtLast(function() {

                $("#product_category_id").trigger("sr-cascade.change", {
                    pageLoad: true
                });
            });
        });
    </script>
@endsection
