<?php
$false = false;
?>
@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'Summary', 'url' => route($routePrefix . '.index')]];
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
                                $field = 'parent_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$tree_list"
                                    value="{{ $model->{$field} }}" class="select2" pleaseSelect="-" />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label" :value="$model->{$field}"
                                    placeholder="Enter {{ $label }}" mandatory="true" />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'short_name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label" :value="$model->{$field}"
                                    placeholder="Enter {{ $label }}" />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'list_display_order';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field type="number" :name="$field" :label="$label"
                                    value="{{ $model->{$field} }}" placeholder="Enter {{ $label }}" mandatory="true"
                                    autocomplete="off" />
                            </div>                            
                            <div class="form-group mb-3 parent_id_block">
                                <div class="d-flex justify-content-center align-items-center">
                                    <div id="parent_uom">
                                        <div class="celec_info">
                                            <div class="label uom_name"></div>
                                            <div class="divider"></div>
                                            <div class="label uom_qty">1</div>
                                        </div>
                                    </div>
                                    <h1>=</h1>
                                    <div id="current_uom">
                                        <div class="celec_info">
                                            <div class="label uom_name"></div>
                                            <div class="divider"></div>
                                            <?php
                                            $field = 'conversion_factor';
                                            $label = get_value_from_list($field_labels, $field, $field);
                                            ?>
                                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                                                :value="$model->{$field}"
                                                class="validate-float"
                                                placeholder="Enter {{ $label }}" 
                                                mandatory="true"
                                                autocomplete="off" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                   
                    <div class="row">
                        <div class="col-md-12 col-xxl-6">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'is_last_node';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.checkbox :id="$field" :name="$field" :label="$label" value="{{ $model->{$field} }}" />
                            </div>
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
                <x-Backend.form-common-footer-buttons />
            </div>

        </div>
    </div>
    </form>

    <script>
        $(function() {
            $.events.execAtLast(function() {

                $("#parent_id").change(function(){
                    var v = $(this).val();

                    if (v)
                    {
                        $(".parent_id_block").show();
                        $(".parent_id_block").find("input,select").removeAttr("disabled");

                        ajaxGetJson("/uom/ajax_get/" + v, function(response){
                            var data = response['data'];
                            $("#parent_uom .uom_name").html(data['name_short_name']);
                        });
                    }
                    else
                    {
                        $(".parent_id_block").hide();
                        $(".parent_id_block").find("input,select").attr("disabled", true);
                    }
                }).trigger("change", {pageLoad : true});

                $("#name, #short_name").on("input, keyup", function(){

                    var name = $("#name").val();
                    var short_name = $("#short_name").val();

                    $("#current_uom .uom_name").html(name + " (" + short_name + ")");
                }).trigger("keyup", {pageLoad : true});
            });
        })
    </script>
@endsection
