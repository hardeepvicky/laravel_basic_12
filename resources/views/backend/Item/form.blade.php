<?php

use App\Models\Task;
use App\Helpers\DateUtility;

$false = false;
?>

@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />
    <div class="card">
        <div class="card-header card-no-border pb-0">
            <a href="{{ route('recurring-task.create') }}">Open Recurring Task Form</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 col-xxl-6">
                    <div class="form-group mb-3">
                        <?php
                        $field = 'name';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                            placeholder="Enter {{ $label }}" mandatory="true" autocomplete="off" />
                    </div>
                    <div class="form-group mb-3">
                        <div class="textarea-wrapper">
                            <?php
                            $field = 'description';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-area :id="$field" :name="$field" :label="$label" rows="3"
                                value="{{ $model->{$field} }}" placeholder="Enter {{ $label }}"
                                class="auto-suggesstion-list" />
                            <div id="{{ $field . '-suggestion-box' }}" class="suggestion-box hidden"></div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <?php
                        $field = 'item_category_id';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$item_category_list"
                            :value="$model->{$field}" class="select2" mandatory="true" />
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'color_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$color_list"
                                    :value="$model->{$field}" class="select2"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'brand_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$brand_list"
                                    :value="$model->{$field}" class="select2"/>
                            </div>
                        </div>       
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'size';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'specification';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <?php
                        $field = 'sku';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.text-field :id="$field" :name="$field" :label="$label" value="{{ $model->{$field} }}"
                            placeholder="Enter {{ $label }}" mandatory="true" readonly="readonly" />
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
                                $field = 'other_files';
                                $label = get_value_from_list($field_labels, $field, $field);
                                $value = implode(',', $model->otherFiles?->pluck('id')->toArray());
                                ?>
                                <span id="choose-other_files" class="btn btn-secondary">Choose Files</span>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                                    :value="$value" multiple="multiple" />
                            </div>
                        </div>
                    </div>                            
                    <div class="form-group mb-3">
                        <?php
                        $field = 'tag_name';
                        $label = get_value_from_list($field_labels, $field, 'Tags');
                        $value = implode(',', $model->tags?->pluck('name')->toArray()) ?? [];
                        ?>
                        <x-Inputs.choice :id="$field" :name="$field" :label="$label" :value="$value"
                            class="choice " placeholder="Enter {{ $label }}" />
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
                <div class="col-md-12 col-xxl-6">
                                      
                </div>
            </div>
            <div class="row">
                <div class="col-xl-8">
                    <x-Backend.form-common-footer-buttons />
                </div>
            </div>
        </div>
    </div>
    </form>

    <script>        
        $(function() {
            var screen_name = '<?= $screen_name ?>';
            var sku_pattern = '<?= $sku_pattern ?>';
            var color, brand, category;

            function create_sku()
            {
                var sku = sku_pattern;

                if (category)
                {
                    console.log(category);
                    sku = sku.replace("{category}", category['short_name']);
                }
                else
                {
                    sku = sku.replace("{category}", "");
                }

                if (brand)
                {
                    console.log(brand);
                    sku = sku.replace("{brand}", brand['short_name']);
                }
                else
                {
                    sku = sku.replace("{brand}", "");
                }

                if (color)
                {
                    console.log(color);
                    sku = sku.replace("{color}", color['short_name']);
                }
                else
                {
                    sku = sku.replace("{color}", "");
                }

                var size = $("#size").val();

                if (size)
                {
                    sku = sku.replace("{size}", size);
                }
                else
                {
                    sku = sku.replace("{size}", "");
                }

                var specification = $("#specification").val();

                if (specification)
                {
                    sku = sku.replace("{specification}", size);
                }
                else
                {
                    sku = sku.replace("{specification}", "");
                }

                //remove multiple hyphine with single and remove hyphine from start and end of string
                sku = sku.replace(/-+/g, "-").replace(/^-+|-+$/g, "");
                sku = sku.toLowerCase();

                $("#sku").val(sku);

                return sku;
            }

            $.events.execAtLast(function() {

                suggestion_list($("#description"), $("#description-suggestion-box"), [], "/tags/ajax_get_list");

                $("#item_category_id").change(function(){
                    var v = $(this).val();
                    category = undefined;

                    if (v)
                    {
                        ajaxGetJson("/item-category/ajax_get/" + v, function(response) {
                            category = response['data'];
                            create_sku();
                        });
                    }
                    else
                    {
                        create_sku();
                    }
                });

                $("#color_id").change(function(){
                    var v = $(this).val();
                    color = undefined;

                    if (v)
                    {
                        ajaxGetJson("/drop-down-value/ajax_get/" + v, function(response) {
                            color = response['data'];
                            create_sku();
                        });
                    }
                    else
                    {
                        create_sku();
                    }
                });

                $("#brand_id").change(function(){
                    var v = $(this).val();
                    brand = undefined;

                    if (v)
                    {
                        ajaxGetJson("/drop-down-value/ajax_get/" + v, function(response) {
                            brand = response['data'];
                            create_sku();
                        });
                    }
                    else
                    {
                        create_sku();
                    }
                });

                $("#size, #specification").keyup(create_sku);
                $("#size, #specification").on("paste", create_sku);

                $("#images").browseGallery({
                    opener: "#choose-images",
                    postfix: "choose-images",
                    type: "image",
                    index_table_id : "document_file_images"
                });

                $("#other_files").browseGallery({
                    opener: "#choose-other_files",
                    postfix: "choose-other_files",
                    index_table_id : "document_file_others"
                });

            });
        })
    </script>
@endsection
