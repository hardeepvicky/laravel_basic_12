<?php
use App\Models\ProductCategoryAttribute;
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
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                    placeholder="Enter {{ $label }}" mandatory="true" />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'short_name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
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
                            <div class="form-group mb-3">
                                <?php
                                $field = 'is_last_node';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.checkbox :id="$field" :name="$field" :label="$label" value="{{ $model->{$field} }}" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 is_last_node-block is_last_node-block-1">
                            <table class="table table-bordered order-column template-table"
                                data-sr-table-template-min-row="0">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width : 50px;">
                                            <span class="sr-table-template-add">
                                                <i class="fas fa-plus-circle text-success icon"></i>
                                            </span>
                                        </th>
                                        <th style="width:30%">{{ get_value_from_list($category_attribute_field_labels, 'name') }}</th>
                                        <th style="width:20%">{{ get_value_from_list($category_attribute_field_labels, 'input_type') }}</th>
                                        <th style="width:30%">{{ get_value_from_list($category_attribute_field_labels, 'dropdown_type') }}</th>
                                        <th style="width:10%">{{ get_value_from_list($category_attribute_field_labels, 'is_active') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="sr-table-template-row hidden">
                                        <td>
                                            <div class="block">
                                                <div class="left-block">
                                                    <div class="sr-table-template-move-up">
                                                        <i class="fas fa-arrow-circle-up icon"></i>
                                                    </div>
                                                    <div class="sr-table-template-move-down">
                                                        <i class="fas fa-arrow-circle-down icon"></i>
                                                    </div>
                                                </div>
                                                <div class="right-block">
                                                    <span class="sr-table-template-delete">
                                                        <i class=" fas fa-times-circle text-danger icon"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <x-inputs.text-field type="hidden" name="productCategoryAttribute[(sr-id)][id]"
                                                label="" />
                                        </td>
                                        <td>
                                            <?php
                                                $field = "name";
                                            ?>
                                            <x-inputs.text-field name="productCategoryAttribute[(sr-id)][{{ $field }}]" label="" class="will-required"/>
                                        </td>
                                        <td>
                                            <?php
                                                $field = "input_type";
                                            ?>
                                            <x-inputs.drop-down name="productCategoryAttribute[(sr-id)][{{ $field }}]" label=""
                                                :list="$input_type_list"
                                                class="will-select2 will-required input_type"
                                            />
                                        </td>
                                        <td>
                                            <div class="input_type_dropdown">
                                                <?php
                                                    $field = "dropdown_type";
                                                ?>
                                                <x-inputs.drop-down name="productCategoryAttribute[(sr-id)][{{ $field }}]" label=""
                                                    class="will-select2 dropdown_type"
                                                />
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                                $field = "is_active";
                                            ?>
                                            <x-inputs.checkbox name="productCategoryAttribute[(sr-id)][{{ $field }}]" label=""/>
                                        </td>
                                    </tr>
                                    @php
                                        $productCategoryAttribute = old('productCategoryAttribute', $productCategoryAttribute ?? []);
                                        unset($productCategoryAttribute['(sr-id)']);
                                    @endphp
                                    @if (isset($productCategoryAttribute))
                                        @foreach ($productCategoryAttribute as $k => $child_arr)
                                            <tr class="" sr-id="{{ $child_arr['id'] }}"
                                                sr-counter="{{ $k }}">
                                                <td>
                                                    <div class="block">
                                                        <div class="left-block">
                                                            <div class="sr-table-template-move-up">
                                                                <i class="fas fa-arrow-circle-up icon"></i>
                                                            </div>
                                                            <div class="sr-table-template-move-down">
                                                                <i class="fas fa-arrow-circle-down icon"></i>
                                                            </div>
                                                        </div>
                                                        <div class="right-block">
                                                            <span class="sr-table-template-delete">
                                                                <i class=" fas fa-times-circle text-danger icon"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="productCategoryAttribute[{{ $k }}][id]" value="{{ $child_arr['id'] ?? '' }}" />
                                                </td>                                                
                                                <td>
                                                    <?php
                                                        $field = "name";
                                                        $value = $child_arr[$field] ?? "";
                                                    ?>
                                                    <x-inputs.text-field name="productCategoryAttribute[{{ $k }}][{{ $field }}]" label="" class="will-required" :value="$value"/>
                                                </td>
                                                <td>
                                                    <?php
                                                        $field = "input_type";
                                                        $value = $child_arr[$field] ?? "";
                                                    ?>
                                                    <x-inputs.drop-down name="productCategoryAttribute[{{ $k }}][{{ $field }}]" label=""
                                                        :list="$input_type_list"
                                                        class="will-select2 will-required input_type"  
                                                        :value="$value"
                                                    />
                                                </td>
                                                <td>
                                                    <div class="input_type_dropdown">
                                                        <?php
                                                            $field = "dropdown_type";
                                                            $value = $child_arr[$field] ?? "";
                                                        ?>
                                                        <x-inputs.drop-down name="productCategoryAttribute[{{ $k }}][{{ $field }}]" label=""
                                                            class="will-select2 dropdown_type"
                                                            :value="$value"
                                                        />
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php
                                                        $field = "is_active";
                                                        $value = $child_arr[$field] ?? "";
                                                    ?>
                                                    <x-inputs.checkbox name="productCategoryAttribute[{{ $k }}][{{ $field }}]" label=""
                                                        :value="$value"
                                                    />
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="text-center" style="width : 80px;">
                                            <span class="sr-table-template-add">
                                                <i class="fas fa-plus-circle text-success icon"></i>
                                            </span>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-xxl-6">
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
            var screen_name = '<?= $screen_name ?>';

            $.events.execAtLast(function() {

                function show_hide_template_field(_trs)
                {
                    if (_trs && _trs.length > 0)
                    {

                    }
                    else
                    {
                        _trs = $(".template-table").find("> tbody > tr").not(".sr-table-template-row");
                    }

                    _trs.each(function(){
                        var _tr = $(this);

                        var input_type = _tr.find(".input_type").val();

                        if (input_type)
                        {
                            _tr.find(".input_type_dropdown").hide();
                            _tr.find(".input_type_dropdown").find("input,select").attr("disabled", true);

                            if (input_type == "{{ ProductCategoryAttribute::INPUT_TYPE_DROPDOWN }}")
                            {
                                _tr.find(".input_type_dropdown").show();
                                _tr.find(".input_type_dropdown").find("input,select").removeAttr("disabled");
                            }
                        }
                    });
                }

                $(".template-table").srTableTemplate({
                    brace_type: "(",
                    afterRowAdd(_table, last_id, _tr) {
                        _tr.find(".will-select2").select2({
                            placeHolder: "Please Select",
                            theme: "bootstrap-5",
                        });

                        _tr.find(".will-required").attr("required", true);

                        show_hide_template_field(_tr);
                    }
                });

                var template_trs = $(".template-table").find("> tbody > tr").not(".sr-table-template-row");

                template_trs.find(".will-required").attr("required", true);

                template_trs.find(".will-select2").select2({
                    placeHolder: "Please Select",
                    theme: "bootstrap-5",
                });

                $(".template-table").on("change", ".input_type", function(){
                    var _tr = $(this).closest("tr");
                    show_hide_template_field(_tr);
                });

                $(".cascade-drop-down").trigger("change", {
                    pageLoad: true
                });

                function show_hide_field() {

                    var is_last_node = $("#is_last_node").prop("checked");
                    is_last_node = is_last_node ? 1 : 0;

                    var selector = ".is_last_node-block";
                    $(selector).hide();
                    $(selector).find("input,select").attr("disabled", true);

                    console.log("hide", selector);

                    var selector = ".is_last_node-block-any, .is_last_node-block-" + is_last_node;

                    if ($(selector).length > 0) {
                        console.log("show", selector);
                        $(selector).show();
                        $(selector).find("input,select").removeAttr("disabled");
                    }
                }

                $("#is_last_node").change(show_hide_field);

                show_hide_field();

                show_hide_template_field();
            })
        })
    </script>
@endsection
