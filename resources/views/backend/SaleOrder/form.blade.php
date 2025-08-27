@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];

    $disabled = isset($screen) && $screen == "edit" ? true : false;
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
                            <?php
                            $field = 'order_no';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field layout="horizontal" :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}" placeholder="Enter {{ $label }}"
                                mandatory="true" disabled="true"/>

                            <?php
                            $field = 'order_from_type';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down layout="horizontal" :id="$field" :name="$field" :label="$label"
                                :list="$order_from_type_list" 
                                :value="$model->{$field}" 
                                class="select2" 
                                mandatory="true" 
                                :disabled="$disabled"
                                />

                            <div class="order_from_type-block order_from_type-block_dealer">
                                <?php
                                $field = 'dealer_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down layout="horizontal" :id="$field" :name="$field" :label="$label"
                                    :list="$dealer_list" 
                                    :value="$model->{$field}" 
                                    class="select2"
                                    :disabled="$disabled"
                                />
                            </div>

                            <div class="order_from_type-block order_from_type-block_customer">
                                <?php
                                $field = 'customer_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down layout="horizontal" :id="$field" :name="$field" :label="$label"
                                    :list="$customer_list" 
                                    :value="$model->{$field}" 
                                    class="select2" 
                                    :disabled="$disabled"
                                    />
                            </div>

                            <div class="order_from_type-block order_from_type-block_lead">
                                <?php
                                $field = 'lead_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down layout="horizontal" :id="$field" :name="$field" :label="$label"
                                    :list="$lead_list" 
                                    :value="$model->{$field}" 
                                    class="select2"
                                    :disabled="$disabled"
                                    />
                            </div>

                            <?php
                            $field = 'estimated_dispatch_date';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field layout="horizontal" :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}" placeholder="Enter {{ $label }}"
                                class="date-picker"
                                mandatory="true" 
                            />
                            
                            <?php
                            $field = 'remarks';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-area layout="horizontal" :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}" 
                                rows="3"
                                placeholder="Enter {{ $label }}" />
                        </div>
                            
                        <div class="col-md-12">
                            <h4 class="b-b-info">
                                Products
                            </h4>
                            <table class="table table-bordered order-column template-table"
                                data-sr-table-template-min-row="0">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width : 8%;">
                                            <span class="sr-table-template-add">
                                                <i class="fas fa-plus-circle text-success icon"></i>
                                            </span>
                                        </th>
                                        <th style="width:30%">
                                            {{ get_value_from_list($sale_order_product_field_labels, 'product_id') }}
                                        </th>
                                        <th style="width:20%">
                                            {{ get_value_from_list($sale_order_product_field_labels, 'make_id') }}
                                        </th>
                                        <th style="width:10%">
                                            {{ get_value_from_list($sale_order_product_field_labels, 'order_qty') }}
                                        </th>
                                        <th style="width:8%">
                                            {{ get_value_from_list($sale_order_product_field_labels, 'available_qty') }}
                                        </th>
                                        <th style="width:8%">
                                            {{ get_value_from_list($sale_order_product_field_labels, 'qty_to_manufacture') }}
                                        </th>
                                        <th>
                                            {{ get_value_from_list($sale_order_product_field_labels, 'remarks') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="sr-table-template-row hidden">
                                        <td>
                                            <div class="block">
                                                <div class="left-block">
                                                    
                                                </div>
                                                <div class="right-block">
                                                    <span class="sr-table-template-delete">
                                                        <i class=" fas fa-times-circle text-danger icon"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <x-inputs.text-field type="hidden" name="sale_order_products[(sr-id)][id]"
                                                label="" />
                                        </td>
                                        <td>
                                            <?php
                                            $field = 'product_id';
                                            ?>
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <x-Inputs.drop-down name="sale_order_products[(sr-id)][{{ $field }}]" :label="$false" :list="$empty_list" class="will-select2 child_product_id"/>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="d-flex align-items-start">
                                                        <span id="opener-product_id" class="btn btn-secondary opener-child_product_id flex-shrink-0">Choose</span>
                                                        <input type="hidden" class="choose-child_product_id" />
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $field = 'make_id';
                                            if ($default_maker)
                                            {
                                                $model->{$field} = $default_maker->id;
                                            }
                                            ?>
                                            <x-Inputs.drop-down id="{{ $field }}-(sr-id)"
                                                name="sale_order_products[(sr-id)][{{ $field }}]" :label="$false"
                                                :list="$maker_list" class="will-required will-select2 make_id" />
                                        </td>
                                        <td>
                                            <?php
                                            $field = 'order_qty';
                                            ?>
                                            <x-Inputs.text-field type="number" id="{{ $field }}-(sr-id)" name="sale_order_products[(sr-id)][{{ $field }}]"
                                                :label="$false"
                                                class="will-required validate-int order_qty" />
                                        </td>
                                        <td>
                                            <?php
                                            $field = 'available_qty';
                                            ?>
                                            <span class="{{ $field }}"></span>
                                            <input type="hidden" name="sale_order_products[(sr-id)][{{ $field }}]" class="{{ $field }}">
                                        </td>
                                        <td>
                                            <?php
                                            $field = 'qty_to_manufacture';
                                            ?>
                                            <span class="{{ $field }}"></span>
                                            <input type="hidden" name="sale_order_products[(sr-id)][{{ $field }}]" class="{{ $field }}">
                                        </td>
                                        <td>
                                            <?php
                                            $field = 'remarks';
                                            ?>
                                            <x-Inputs.text-field name="sale_order_products[(sr-id)][{{ $field }}]"
                                                :label="$false" />
                                        </td>
                                    </tr>
                                    @php
                                        $sale_order_products = old('sale_order_products', $model->saleOrderProduct?->toArray() ?? []);
                                        unset($sale_order_products['(sr-id)']);
                                    @endphp
                                    @if (isset($sale_order_products))
                                        @foreach ($sale_order_products as $k => $child_arr)
                                            <tr class="" sr-id="{{ $child_arr['id'] }}"
                                                sr-counter="{{ $k }}">
                                                <td>
                                                    <div class="block">
                                                        <div class="left-block">
                                                            
                                                        </div>
                                                        <div class="right-block">
                                                            <span class="sr-table-template-delete">
                                                                <i class=" fas fa-times-circle text-danger icon"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="sale_order_products[{{ $k }}][id]"
                                                        value="{{ $child_arr['id'] ?? '' }}" />
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'product_id';
                                                    $value = $child_arr[$field] ?? '';
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-sm-8">
                                                            <x-Inputs.drop-down name="sale_order_products[{{ $k }}][{{ $field }}]" 
                                                                :label="$false" :list="$empty_list" :value="$value"
                                                                class="will-select2 child_product_id"/>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="d-flex align-items-start">
                                                                <span id="opener-product_id" class="btn btn-secondary opener-child_product_id flex-shrink-0">Choose</span>
                                                                <input type="hidden" class="choose-child_product_id" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'make_id';
                                                    $value = $child_arr[$field] ?? '';
                                                    ?>
                                                    <x-Inputs.drop-down id="{{ $field }}-{{ $k }}"
                                                        name="sale_order_products[{{ $k }}][{{ $field }}]" :label="$false" :value="$value"
                                                        :list="$maker_list" class="will-required will-select2 make_id" />
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'order_qty';
                                                    $value = $child_arr[$field] ?? '';
                                                    ?>
                                                    <x-Inputs.text-field type="number" id="{{ $field }}-{{ $k }}" name="sale_order_products[{{ $k }}][{{ $field }}]"
                                                        :label="$false" :value="$value"
                                                        class="will-required validate-int order_qty" />
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'available_qty';
                                                    $value = $child_arr[$field] ?? '';
                                                    ?>
                                                    <span class="{{ $field }}"></span>
                                                    <input type="hidden" name="sale_order_products[{{ $k }}][{{ $field }}]" class="{{ $field }}"  :value="$value">
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'qty_to_manufacture';
                                                    $value = $child_arr[$field] ?? '';
                                                    ?>
                                                    <span class="{{ $field }}"></span>
                                                    <input type="hidden" name="sale_order_products[{{ $k }}][{{ $field }}]" class="{{ $field }}" :value="$value">
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'remarks';
                                                    $value = $child_arr[$field] ?? '';
                                                    ?>
                                                    <x-Inputs.text-field name="sale_order_products[{{ $k }}][{{ $field }}]" class="{{ $field }}"
                                                        :label="$false" />
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

            <x-Backend.form-common-footer-buttons />
        </div>
    </div>
    </form>

    <script>
        $(function() {
            var model_id = '<?= $model->id ?? 0 ?>';

            function order_from_type_toggle()
            {
                var order_from_type = $("#order_from_type").val();

                var selector = ".order_from_type-block";
                $(selector).hide();
                $(selector).find("input,select").attr("disabled", true);
                console.log("hide", selector);

                if (order_from_type)
                {
                    order_from_type = order_from_type.toLowerCase().trim();
                    selector = ".order_from_type-block_any, .order_from_type-block_" + order_from_type;
                    console.log("show", selector);
                    $(selector).show();
                    $(selector).find("input,select").removeAttr("disabled");
                }
            }

            $("#order_from_type").change(order_from_type_toggle);

            function load_qty_fields(_tr)
            {
                var product_id = _tr.find("select.child_product_id").val();
                var make_id = _tr.find("select.make_id").val();
                var order_qty = _tr.find("input.order_qty").val();

                if (product_id && make_id && order_qty)
                {
                    var url = `/sale-order-ajax_get_available_qty_and_qty_to_manufacture/${product_id}/${make_id}/${order_qty}`;
                    ajaxGetJson(url, function(response){
                        console.log(response);

                        _tr.find("span.available_qty").html(response['available_qty']);
                        _tr.find("input.available_qty").val(response['available_qty']);

                        _tr.find("span.qty_to_manufacture").html(response['qty_to_manufacture']);
                        _tr.find("input.qty_to_manufacture").val(response['qty_to_manufacture']);
                    });
                }
            }

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

                    $(target).html(html);

                    var v = $(target).attr("data-value");

                    if (v) {
                        $(target).val(v).trigger("change");
                    }

                });
            }

            function init_child_product_id(_tr, last_id) {

                _tr.find(".choose-child_product_id").browseProductGallery({
                    opener: _tr.find(".opener-child_product_id"),
                    postfix: "choose-child_product_id",
                    onSelect: function(id_list) {
                        _tr.find(".child_product_id").attr("data-value", id_list[0]);
                        load_product_list(_tr.find(".child_product_id"));
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

                    load_product_list(_tr.find(".child_product_id"));

                    init_child_product_id(_tr);
                }
            });

            var trs = $(".template-table").find(">tbody>tr").not(".sr-table-template-row");
            trs.each(function(index, _tr){

                load_product_list($(_tr).find(".child_product_id"));

                init_child_product_id($(_tr), index);
            });

            $(document).on("change", ".child_product_id, .make_id", function(){
                load_qty_fields($(this).closest("tr"));
            });
            
            $(document).on("input", "input.order_qty", function(){
                load_qty_fields($(this).closest("tr"));
            });

            $.events.execAtLast(function() {

                order_from_type_toggle();
            });
        });
    </script>
@endsection
