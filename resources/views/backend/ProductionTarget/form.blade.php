<?php
use App\Models\User;
?>

@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" class="i-validate" />

    <div class="card">
        <div class="card-header card-no-border pb-0">
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 col-xxl-6">
                    <?php
                    $field = 'branch_id';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$branch_list"
                        :value="$model->{$field}"
                        layout="horizontal"
                        class="select2 cascade-drop-down"
                        data-sr-cascade-target="#responsible_user_id"
                        data-sr-cascade-url="/user/ajax_get_list?branch_id={v}"
                        mandatory="true" />

                    <?php
                    $field = 'responsible_user_id';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$empty_list"
                        :value="$model->{$field}"
                        layout="horizontal"
                        class="select2"
                        mandatory="true" />

                    <?php
                    $field = 'product_id';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <label class="form-label">
                                {{ $label }}
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                        :list="$product_list"
                                        :value="$model->{$field}"
                                        class="select2"
                                        mandatory="true"
                                    />
                                </div>
                                <span id="opener-product_id" class="btn btn-secondary opener-product_id flex-shrink-0">Choose</span>
                            </div>
                        </div>
                    </div>

                    <?php
                    $field = 'target_date';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                        :value="$model->{$field}" 
                        class="select2 date-picker"
                        layout="horizontal"
                        mandatory="true" 
                        placeholder="Enter {{ $label }}" 
                        />

                    <?php
                    $field = 'qty';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.text-field layout="horizontal" :id="$field" :name="$field" :label="$label"
                        :value="$model->{$field}" 
                        class="validate-float"
                        placeholder="Enter {{ $label }}" />

                    <?php
                    $field = 'remarks';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.text-area layout="horizontal" :id="$field" :name="$field" :label="$label"
                        :value="$model->{$field}" 
                        rows="3"
                        placeholder="Enter {{ $label }}" />
                </div>
            </div>
        </div>
        <x-Backend.form-common-footer-buttons />
    </div>
    </form>

    <script type="text/javascript">
        $(function() {
            function load_product_list(target)
            {
                ajaxGetJson('{{ route("product.ajax_get_list") }}', function(response){

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
                    
                    var target = "#product_id";

                    $(target).html(html);

                    var v = $(target).attr("data-value");

                    if (v) {
                        $(target).val(v).trigger("change");
                    }
                });
            }

            $("#product_id").browseProductGallery({
                opener: $("#opener-product_id"),
                postfix: "choose-product_id",
                onSelect: function(id_list) {
                    $("#product_id").attr("data-value", id_list[0]);
                    load_product_list();
                }
            });

            $.events.execAtLast(function() {
                $("#branch_id").trigger("change", {
                    pageLoad: true
                });
            });
        });
    </script>
@endsection
