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

    <div class="row">
        <div class="col-xxxl-10">
            <div class="card">
                <div class="card-header card-no-border pb-0">
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 col-xxl-6">
                                <?php
                                $field = 'name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" layout="horizontal" mandatory="true" autocomplete="off" />

                                <?php
                                $field = 'specification';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" layout="horizontal" />

                                <?php
                                $field = 'size';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" layout="horizontal" />

                                <?php
                                $field = 'root_item_category_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" 
                                    :list="$root_item_category_list"
                                    :value="$model->{$field}"
                                    layout="horizontal"
                                    class="select2 cascade-drop-down"
                                    data-sr-cascade-target="#parent_item_category_id"
                                    data-sr-cascade-url="/item-category/ajax_get_list/{v}"
                                    mandatory="true" 
                                    />
                                    
                                <?php
                                $field = 'parent_item_category_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        @if (isset($label) && $label)
                                            <label class="form-label">
                                                {{ $label }}
                                                <span class="mandatory">*</span>
                                            </label>
                                        @endif
                                    </div>                                
                                    <div class="col-sm-9">
                                        <div class="d-flex align-items-start">
                                            <span class="btn btn-outline-info summary-action-button open_parent_category_add_form" style="padding: 9px 12px;">
                                                <i class="fa-solid fa-plus"></i>
                                            </span>
                                            <div class="flex-grow-1">
                                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :list="$empty_list"
                                                    :value="$model->{$field}" 
                                                    class="select2 cascade-drop-down"
                                                    data-sr-cascade-target="#child_item_category_id"
                                                    data-sr-cascade-url="/item-category/ajax_get_list/{v}"
                                                    />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                $field = 'child_item_category_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        @if (isset($label) && $label)
                                            <label class="form-label">
                                                {{ $label }}
                                                <span class="mandatory">*</span>
                                            </label>
                                        @endif
                                    </div>                                
                                    <div class="col-sm-9">
                                        <div class="d-flex align-items-start">
                                            <span class="btn btn-outline-info summary-action-button open_child_category_add_form" style="padding: 9px 12px;">
                                                <i class="fa-solid fa-plus"></i>
                                            </span>
                                            <div class="flex-grow-1">
                                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :list="$empty_list"
                                                    :value="$model->{$field}" class="select2" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        
                                <?php
                                $field = 'child_item_category_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                

                                <?php
                                $field = 'item_brand_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$empty_list"
                                    :value="$model->{$field}" class="select2" layout="horizontal" />

                                <?php
                                $field = 'sku';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" layout="horizontal"  mandatory="true" readonly="readonly" />

                                <?php
                                $field = 'part_no';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" layout="horizontal" />

                                <?php
                                $field = 'dept_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$dept_list"
                                    :value="$model->{$field}" 
                                    class="select2" 
                                    layout="horizontal" 
                                    class="select2 cascade-drop-down"
                                    data-sr-cascade-target="#sub_dept_ids"
                                    data-sr-cascade-url="/sub-department/ajax_get_list/{v}"
                                    mandatory="true" 
                                    />

                                <?php
                                $field = 'is_for_all_sub_dept';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <div class="row">
                                    <div class="col-sm-3">
                                    </div>
                                    <div class="col-sm-9">
                                        <x-Inputs.checkbox :id="$field" :name="$field" :label="$label" :value="$model->{$field}" />
                                    </div>
                                </div>

                                <?php
                                $field = 'sub_dept_ids';
                                $label = "Sub Department";
                                $value = implode(',', $model->subDepartment?->pluck('id')->toArray());
                                ?>
                                <x-Inputs.drop-down :id="$field" name="sub_dept_ids[]" :label="$label"
                                    :list="$empty_list" 
                                    :value="$value"
                                    layout="horizontal"
                                    class="select2"
                                    multiple="true"
                                />

                                

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
                                        <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                                            :value="$value" multiple="multiple" />
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
                                        <span id="choose-other_files" class="btn btn-secondary">Choose Files</span>
                                        <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                                            :value="$value" multiple="multiple" />
                                    </div>
                                </div>

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
                                        $field = 'is_active';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.checkbox :name="$field" :label="$label"
                                            value="{{ $model->{$field} }}" />
                                            </div>
                                    </div>
                                </div>
                                
                    </div>
                    <div class="row">
                        <div class="col-xl-8">
                            <x-Backend.form-common-footer-buttons />
                        </div>
                    </div>
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

                var name = $("#name").val();

                if (name)
                {
                    sku = sku.replace("{name}", name);
                }
                else
                {
                    sku = sku.replace("{name}", "");
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
                    sku = sku.replace("{specification}", specification);
                }
                else
                {
                    sku = sku.replace("{specification}", "");
                }

                //replace char except hyphine and underscore and remove hyphine from start and end of string
                sku = sku.replace(/[^a-zA-Z0-9-_.]/g, '-').replace(/-+/g, '-').replace(/^-+|-+$/g, "");
                sku = sku.toLowerCase();

                $("#sku").val(sku);

                return sku;
            }

            $("#parent_item_category_id").change(function(){
                var v = $(this).val();

                if (v)
                {
                    ajaxGetJson("/item-brand/ajax_get_list/" + v, function(response) {
                        var html = "";
                        var html = "<option value=''>Please Select</option>";

                        for (var i in response["data"]) {
                            var k = response["data"][i]["id"];
                            var v = response["data"][i]["name"];

                            var attr = 'value="' + k + '"';

                            if (typeof response["data"][i]["auto_select"] != "undefined") {
                                if (response["data"][i]["auto_select"]) {
                                    attr += ' selected="selected"';
                                }
                            }

                            html += '<option ' + attr + '>' + v + "</option>";
                        }

                        var target = "#item_brand_id";

                        $(target).html(html);

                        var v = $(target).attr("data-value");

                        if (v) {
                            $(target).val(v).trigger("change");
                        }
                    });
                }
                else
                {
                    create_sku();
                }
            }).trigger("change", {pageLoad : true});


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
            }).trigger("change", {pageLoad : true});

            $("#item_brand_id").change(function(){
                var v = $(this).val();
                brand = undefined;

                if (v)
                {
                    ajaxGetJson("/item-brand/ajax_get/" + v, function(response) {
                        brand = response['data'];
                        create_sku();
                    });
                }
                else
                {
                    create_sku();
                }
            }).trigger("change", {pageLoad : true});

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

            $("#is_for_all_sub_dept").change(function(){
                if(this.checked)
                {
                    $("#sub_dept_ids").attr("disabled", "true");
                }
                else
                {
                    $("#sub_dept_ids").removeAttr("disabled");
                }
            }).trigger("change", {pageLoad : true});

            $(".open_parent_category_add_form").click(function(){
                var cat_id = $("#root_item_category_id").val();

                if (!cat_id)
                {
                    $.events.onUserError("Please Select Root Category");
                    return false;
                }

                $.loader.setInfo("Redirecting..").show();

                window.location.href = "/item-category/create?parent_id=" + cat_id;
            });

            $(".open_child_category_add_form").click(function(){
                var cat_id = $("#parent_item_category_id").val();

                if (!cat_id)
                {
                    $.events.onUserError("Please Select Parent Category");
                    return false;
                }

                $.loader.setInfo("Redirecting..").show();

                window.location.href = "/item-category/create?parent_id=" + cat_id;
            });

            $.events.execAtLast(function() {
                $("#name, #size, #specification").on("input keyup", create_sku);

                $("#root_item_category_id, #dept_id").trigger("change", {pageLoad : true});
            });
        })
    </script>
@endsection
