@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />

    <div class="row">
        <div class="col-xxl-10">
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
                                    value="{{ $model->{$field} }}" class="select2" mandatory="true" />
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
                                $field = 'short_name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'description';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-area :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = "main_image_id";
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label" :value="$model->{$field}" />                        
                                <span id="choose-main-image" class="btn btn-secondary">Choose Main Image</span>
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = "productFile";
                                $label = get_value_from_list($field_labels, $field, $field);
                                $value = implode(',', $model->productFile?->pluck('document_file_id')->toArray());
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label" :value="$value" multiple="multiple"  />                        
                                <span id="files-choose" class="btn btn-secondary">Choose Files</span>
                            </div>
                            <div id="load_field_block">
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'list_display_order';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.text-field type="number" :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                            placeholder="Enter {{ $label }}" mandatory="true" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-xl-6" style="padding-top: 30px">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'is_active';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.checkbox :name="$field" :label="$label" value="{{ $model->{$field} }}" />
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
        $(function(){
            $("#product_category_id").change(function(){
                var v = $(this).val();

                $("#load_field_block").html("");

                if (v)
                {
                    $.loader.init().show();
                    $("#load_field_block").load("/product/ajax_load_attribute_fields/" + v + "/" + id, function(){
                        $.loader.hide();
                    })
                }
            }).trigger("change", {pageLoad : true});

            $("#main_image_id").browseGallery({
                opener : "#choose-main-image",
                postfix : "choose-main-image",
                type : "image"
            });

            $("#productFile").browseGallery({
                opener : "#files-choose"
            });

            $.events.execAtLast(function(){
                
            })
        });
    </script>
@endsection
