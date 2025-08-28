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
                            ?>
                            <x-Inputs.text-field type="hidden" :id="$field" :name="$field" :label="$false" :value="$model->{$field}" />

                            <?php
                                $field = 'contact_id';
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
                                        <span class="btn btn-outline-info summary-action-button open_contact_modal" style="padding: 9px 12px;">
                                            <i class="fa-solid fa-user"></i>
                                        </span>
                                        <div class="flex-grow-1">
                                            <select id="{{ $field }}" name="{{ $field }}" class="form-select select2" required="true">
                                                <option value="">Please Select</option>
                                                @foreach ($contact_list as $k => $t)
                                                    @php
                                                        $attr = $model->order_from_type == $t['type'] && $t['ref_id'] == $model->{$field} ? 'selected="selected"' : '';
                                                    @endphp
                                                    <option value="{{ $k }}" {!! $attr !!}>
                                                        {{ $t['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @error($field)
                                                <div class="input-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                            $field = 'estimated_dispatch_date';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field layout="horizontal" :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}" placeholder="Enter {{ $label }}"
                                class="custom-date-picker"
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
                                        <th style="width:20%">
                                            {{ get_value_from_list($sale_order_product_field_labels, 'product_id') }}
                                        </th>
                                        <th style="width:10%">
                                            {{ get_value_from_list($sale_order_product_field_labels, 'make_id') }}
                                        </th>
                                        <th style="width:8%">
                                            {{ get_value_from_list($sale_order_product_field_labels, 'order_qty') }}
                                        </th>
                                        <th style="width:8%">
                                            {{ get_value_from_list($sale_order_product_field_labels, 'rate') }}
                                        </th>
                                        <th style="width:8%">
                                            {{ get_value_from_list($sale_order_product_field_labels, 'amount') }}
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
                                            $field = 'rate';
                                            ?>
                                            <x-Inputs.text-field id="{{ $field }}-(sr-id)" name="sale_order_products[(sr-id)][{{ $field }}]"
                                                :label="$false"
                                                class="will-required validate-float rate" />
                                        </td>
                                        <td>
                                            <?php
                                            $field = 'amount';
                                            ?>
                                            <x-Inputs.text-field id="{{ $field }}-(sr-id)" name="sale_order_products[(sr-id)][{{ $field }}]"
                                                :label="$false"
                                                class="will-required validate-float amount" readonly="true"/>
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
                                                    $field = 'rate';
                                                    $value = $child_arr[$field] ?? '';
                                                    ?>
                                                    <x-Inputs.text-field id="{{ $field }}-{{ $k }}" name="sale_order_products[{{ $k }}][{{ $field }}]"
                                                        :label="$false" :value="$value"
                                                        class="will-required validate-float rate" />
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'amount';
                                                    $value = $child_arr[$field] ?? '';
                                                    ?>
                                                    <x-Inputs.text-field id="{{ $field }}-{{ $k }}" name="sale_order_products[{{ $k }}][{{ $field }}]"
                                                        :label="$false" :value="$value"
                                                        class="will-required validate-float amount" readonly="true"/>
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
                        
                        <div class="col-md-12 col-xxl-6 mt-3">
                            <?php
                                $field = 'total_amount';
                                $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    @if (isset($label) && $label)
                                        <label class="form-label">
                                            {{ $label }}
                                        </label>
                                    @endif
                                </div>                                
                                <div class="col-sm-3">
                                    <input type="hidden" name="{{ $field }}" value="{{ $model->{$field} }}" class="{{ $field }}"/>
                                    <span class="{{ $field }}">{{ $model->{$field} }}</span>
                                </div>
                            </div>

                            <?php
                            $field = 'discount_per';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field layout="horizontal" :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}" 
                                class="validate-float validate-less-than"
                                data-less-than-from="99"
                                horizontalSecondColumnLength="3"
                                placeholder="Enter {{ $label }}"
                                mandatory="true" 
                            />

                            <?php
                                $field = 'discount';
                                $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    @if (isset($label) && $label)
                                        <label class="form-label">
                                            {{ $label }}
                                        </label>
                                    @endif
                                </div>                                
                                <div class="col-sm-3">
                                    <input type="hidden" id="{{ $field }}" name="{{ $field }}" value="{{ $model->{$field} }}" class="{{ $field }}"/>
                                    <span class="{{ $field }}">{{ $model->{$field} }}</span>
                                </div>
                            </div>

                            <?php
                                $field = 'payable_amount';
                                $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    @if (isset($label) && $label)
                                        <label class="form-label">
                                            {{ $label }}
                                        </label>
                                    @endif
                                </div>                                
                                <div class="col-sm-3">
                                    <input type="hidden" id="{{ $field }}" name="{{ $field }}" value="{{ $model->{$field} }}" class="{{ $field }}"/>
                                    <span class="big_amount {{ $field }}">{{ $model->{$field} }}</span>
                                </div>
                            </div>

                            <?php
                            $field = 'paid_amount';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field layout="horizontal" :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}" 
                                class="validate-float"
                                horizontalSecondColumnLength="3"
                                placeholder="Enter {{ $label }}"
                                mandatory="true" 
                            />
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
            var currency_formatter = new Intl.NumberFormat('en-IN', { 
                style: 'currency', 
                currency: 'INR' 
            });

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

            $(".open_contact_modal").browseContact({
                selectedValues : 0,
                multiple : 0,
                onSelect : function(id_list)
                {
                    var obj = id_list[0];

                    console.log(obj);

                    $("#contact_id").val(obj.type + "." + obj.id).trigger("change");
                    $("#order_from_type").val(obj.type);
                }
            });

            var estimated_dispatch_date = flatpickr("#estimated_dispatch_date", {
                defaultDate: new Date(),
                dateFormat: constants.dateFormat,
                onChange: function(selectedDates, dateStr, instance) {
                    const selectedDate = selectedDates[0];
                    if (selectedDate) {
                    }
                }
            });

            function cal_amount(_tr)
            {
                var qty = _tr.find("input.order_qty").val();
                qty = qty ? parseFloat(qty) : 0;

                var rate = _tr.find("input.rate").val();
                rate = rate ? parseFloat(rate) : 0;

                var amount = qty * rate;

                _tr.find(".amount").val(amount.toFixed(2));

                cal_grand_amount();
            }

            function cal_grand_amount()
            {
                var total_amount = 0;
                $("input.amount").each(function(){
                    var v = $(this).val();
                    v = v ? parseFloat(v) : 0;
                    total_amount += v;
                });

                total_amount = Math.round(total_amount);

                $("input.total_amount").val(total_amount);
                $("span.total_amount").html(currency_formatter.format(total_amount));

                var discount_per = $("#discount_per").val();
                discount_per = discount_per ? parseFloat(discount_per) : 0;
                var discount = 0;

                if (discount_per > 0)
                {
                    discount = total_amount * discount_per / 100;
                    discount = Math.round(discount);
                }

                $("input.discount").val(discount);
                $("span.discount").html(currency_formatter.format(discount));

                var payable_amount = total_amount - discount;

                $("input.payable_amount").val(payable_amount);
                $("span.payable_amount").html(currency_formatter.format(payable_amount));

            }

            $(".order_qty, .rate").on("input", function(){
                cal_amount($(this).closest("tr"));
            });

            $("#discount_per").on("input", function(){
                cal_grand_amount();
            });

            $.events.execAtLast(function() {

                order_from_type_toggle();
            });
        });
    </script>
@endsection
