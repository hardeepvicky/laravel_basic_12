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
                                    value="{{ $model->{$field} }}" class="select2 cascade-drop-down"
                                    data-sr-cascade-target="#product_id"
                                    data-sr-cascade-url="/product-ajax_get_list?product_category_id={v}" mandatory="true" />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'product_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$empty_list"
                                    value="{{ $model->{$field} }}" class="select2" mandatory="true" />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'make_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$maker_list" value="{{ $model->{$field} }}" class="select2" mandatory="true" />
                            </div>

                            <div class="form-group mb-3">
                                <?php
                                $field = 'type';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$type_list" value="{{ $model->{$field} }}" class="select2" mandatory="true" />
                            </div>

                            <div class="form-group mb-3 type-block type-block_auto">
                                <?php
                                $field = 'no_of_serials';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                                    value="{{ $model->{$field} }}" placeholder="Enter {{ $label }}" mandatory="true"
                                    max="1000" />
                            </div>

                            <div class="form-group mb-3 type-block type-block_manual">
                                <?php
                                $field = 'serial_no';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                                    value="{{ $next_serial_no }}" placeholder="Enter {{ $label }}"
                                    mandatory="true" />
                            </div>
                        </div>
                        <div class="col-md-12 col-xxl-12">
                            <div class="type-block type-block_manual">
                                <h4 class="b-b-info">
                                    Sub Products
                                </h4>
                                <table class="table table-bordered order-column template-table"
                                    data-sr-table-template-min-row="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width : 50px;">
                                                <span class="sr-table-template-add">
                                                    <i class="fas fa-plus-circle text-success icon"></i>
                                                </span>
                                            </th>
                                            <th style="width:30%">
                                                {{ get_value_from_list($field_labels, 'product_category_id') }}
                                            </th>
                                            <th style="width:30%">
                                                {{ get_value_from_list($field_labels, 'product_id') }}
                                            </th>
                                            <th style="width:30%">
                                                {{ get_value_from_list($field_labels, 'serial_no') }}
                                            </th>
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
                                                <x-inputs.text-field type="hidden" name="children[(sr-id)][id]"
                                                    label="" />
                                            </td>
                                            <td>
                                                <?php
                                                $field = 'product_category_id';
                                                ?>
                                                <x-Inputs.drop-down :id="$field"
                                                    name="children[(sr-id)][{{ $field }}]" :label="$false"
                                                    :list="$product_category_list" value="{{ $model->{$field} }}"
                                                    class="will-select2 will-required cascade-drop-down"
                                                    data-sr-cascade-target="#product_id-(sr-id)"
                                                    data-sr-cascade-url="/product-ajax_get_list?product_category_id={v}" />
                                            </td>
                                            <td>
                                                <?php
                                                $field = 'product_id';
                                                ?>
                                                <x-Inputs.drop-down id="{{ $field }}-(sr-id)" 
                                                    name="children[(sr-id)][{{ $field }}]"
                                                    :label="$false" :list="$empty_list" value="{{ $model->{$field} }}"
                                                    class="will-select2 will-required cascade-drop-down"
                                                    data-sr-cascade-target="#product_serial_id-(sr-id)"
                                                    data-sr-cascade-url="/product-serial-ajax_get_unlinked_list?product_serial_id={{ $model->id ?? 0 }}&product_id={v}" />
                                            </td>
                                            <td>
                                                <?php
                                                $field = 'product_serial_id';
                                                ?>
                                                <x-Inputs.drop-down id="{{ $field }}-(sr-id)" name="children[(sr-id)][{{ $field }}]"
                                                    :label="$false" :list="$empty_list" value="{{ $model->{$field} }}"
                                                    class="will-select2 will-required" />
                                            </td>
                                        </tr>
                                        @php
                                            $children = old('children', $children ?? []);
                                            unset($children['(sr-id)']);
                                        @endphp
                                        @if (isset($children))
                                            @foreach ($children as $k => $child_arr)
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
                                                        <input type="hidden" name="children[{{ $k }}][id]"
                                                            value="{{ $child_arr['id'] ?? '' }}" />
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $field = 'product_category_id';
                                                        $value = $child_arr[$field] ?? '';
                                                        ?>
                                                        <x-Inputs.drop-down :id="$field"
                                                            name="children[{{ $k }}][{{ $field }}]"
                                                            :label="$false" :list="$product_category_list" :value="$value"
                                                            class="select2 cascade-drop-down"
                                                            data-sr-cascade-target="#product_id-{{ $k }}"
                                                            data-sr-cascade-url="/product-ajax_get_list?product_category_id={v}"
                                                            mandatory="true" />
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $field = 'product_id';
                                                        $value = $child_arr[$field] ?? '';
                                                        ?>
                                                        <x-Inputs.drop-down id="{{ $field }}-{{ $k }}"
                                                            name="children[{{ $k }}][{{ $field }}]" :label="$false" :list="$empty_list"
                                                            :value="$value" class="select2 cascade-drop-down"
                                                            data-sr-cascade-target="#product_serial_id-{{ $k }}"
                                                            data-sr-cascade-url="/product-serial-ajax_get_unlinked_list?product_serial_id={{ $model->id ?? 0 }}&product_id={v}"
                                                            mandatory="true" />
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $field = 'product_serial_id';
                                                        $value = $child_arr[$field] ?? '';
                                                        ?>
                                                        <x-Inputs.drop-down id="{{ $field }}-{{ $k }}"
                                                            name="children[{{ $k }}][{{ $field }}]" :label="$false" :list="$empty_list"
                                                            :value="$value" class="select2" mandatory="true" />
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
                    </div>
                </div>
            </div>

            <x-Backend.form-common-footer-buttons />
        </div>
    </div>
    </form>

    <script>
        $(function() {

            function show_hide_field() {

                var type = $("#type").val();

                var selector = ".type-block";
                $(selector).hide();
                $(selector).find("input,select").attr("disabled", true);

                console.log("hide", selector);

                var selector = ".type-block_any, .type-block_" + type;

                if ($(selector).length > 0) {
                    console.log("show", selector);
                    $(selector).show();
                    $(selector).find("input,select").removeAttr("disabled");
                }
            }

            $("#type").change(show_hide_field);

            $(".template-table").srTableTemplate({
                brace_type: "(",
                afterRowAdd(_table, last_id, _tr) {
                    _tr.find(".will-select2").select2({
                        placeHolder: "Please Select",
                        theme: "bootstrap-5",
                    });

                    _tr.find(".cascade-drop-down").cascade({
                        beforeGet: function(src, url) {
                            $.loader.init().setInfo("Fetching...").show();
                            return url;
                        },
                        afterGet: function(src, dest, response) {
                            $.loader.init().hide();
                            return response;
                        },
                    })

                    _tr.find(".will-required").attr("required", true);
                }
            });

            var trs = $(".template-table").find(">tbody>tr").not(".sr-table-template-row");
            trs.find(".cascade-drop-down").cascade({
                beforeGet: function(src, url) {
                    $.loader.init().setInfo("Fetching...").show();
                    return url;
                },
                afterGet: function(src, dest, response) {
                    $.loader.init().hide();
                    return response;
                },
            }).trigger("sr-cascade.change", {
                pageLoad: true
            });

            $.events.execAtLast(function() {

                show_hide_field();

                $("#product_category_id").trigger("sr-cascade.change", {
                    pageLoad: true
                });
            });
        });
    </script>
@endsection
