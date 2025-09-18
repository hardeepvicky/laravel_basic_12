@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];

    $disabled = isset($screen) && $screen == "edit" ? true : false;
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />

    @if(isset($requirement_ids))
        @foreach($requirement_ids as $requirement_id)
            <input type="hidden" name="requirement_ids[]" value="{{ $requirement_id }}" />
        @endforeach
    @endif

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
                                $field = 'party_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                :list="$party_list"
                                :value="$model->{$field}"
                                layout="horizontal"
                                class="select2"
                                mandatory="true" />

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
                                            {{ get_value_from_list($purchase_order_item_field_labels, 'item_id') }}
                                        </th>
                                        <th style="width:8%">
                                            {{ get_value_from_list($purchase_order_item_field_labels, 'qty') }}
                                        </th>
                                        <th style="width:10%">
                                            {{ get_value_from_list($purchase_order_item_field_labels, 'uom_id') }}
                                        </th>
                                        <th style="width:8%">
                                            {{ get_value_from_list($purchase_order_item_field_labels, 'rate') }}
                                        </th>
                                        <th style="width:8%">
                                            {{ get_value_from_list($purchase_order_item_field_labels, 'amount') }}
                                        </th>                                        
                                        <th>
                                            {{ get_value_from_list($purchase_order_item_field_labels, 'remarks') }}
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
                                            <x-inputs.text-field type="hidden" name="purchase_order_items[(sr-id)][id]" label="" />
                                        </td>
                                        <td>
                                            <?php
                                            $field = 'item_id';
                                            ?>
                                            <x-Inputs.drop-down name="purchase_order_items[(sr-id)][{{ $field }}]" :label="$false" :list="$item_list" 
                                                class="will-select2 item_id"
                                                data-sr-cascade-target="#uom_id-(sr-id)"
                                                data-sr-cascade-url="/item/ajax_get_child_uom_list/{v}"
                                            />
                                        </td>
                                        <td>
                                            <?php
                                            $field = 'qty';
                                            ?>
                                            <x-Inputs.text-field type="number" id="{{ $field }}-(sr-id)" name="purchase_order_items[(sr-id)][{{ $field }}]"
                                                :label="$false"
                                                class="will-required validate-int qty" />
                                        </td>
                                        <td>
                                            <?php
                                            $field = 'uom_id';
                                            ?>
                                            <x-Inputs.drop-down id="{{ $field }}-(sr-id)" name="purchase_order_items[(sr-id)][{{ $field }}]" :label="$false" :list="$empty_list" />
                                        </td>
                                        <td>
                                            <?php
                                            $field = 'rate';
                                            ?>
                                            <x-Inputs.text-field id="{{ $field }}-(sr-id)" name="purchase_order_items[(sr-id)][{{ $field }}]"
                                                :label="$false"
                                                class="will-required validate-float rate" />
                                        </td>
                                        <td>
                                            <?php
                                            $field = 'amount';
                                            ?>
                                            <x-Inputs.text-field id="{{ $field }}-(sr-id)" name="purchase_order_items[(sr-id)][{{ $field }}]"
                                                :label="$false"
                                                class="will-required validate-float amount" readonly="true"/>
                                        </td>
                                        <td>
                                            <?php
                                            $field = 'remarks';
                                            ?>
                                            <x-Inputs.text-field name="purchase_order_items[(sr-id)][{{ $field }}]"
                                                :label="$false" />
                                        </td>
                                    </tr>
                                    @php
                                        $purchase_order_items = old('purchase_order_items', isset($requirements) ? $requirements : ( $model->saleOrderProduct?->toArray() ?? []));
                                        unset($purchase_order_items['(sr-id)']);
                                    @endphp
                                    @if (isset($purchase_order_items))
                                        @foreach ($purchase_order_items as $k => $child_arr)
                                            <tr class="" sr-id="{{ $child_arr['id'] ?? '' }}"
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
                                                    <input type="hidden" name="purchase_order_items[{{ $k }}][id]"
                                                        value="{{ $child_arr['id'] ?? '' }}" />
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'item_id';
                                                    $value = $child_arr[$field] ?? '';
                                                    ?>
                                                    <x-Inputs.drop-down name="purchase_order_items[{{ $k }}][{{ $field }}]" 
                                                        :label="$false"
                                                        :list="$item_list"                                                         
                                                        :value="$value"
                                                        class="will-select2 item_id"
                                                        data-sr-cascade-target="#uom_id-{{ $k }}"
                                                        data-sr-cascade-url="/item/ajax_get_child_uom_list/{v}"
                                                    />
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'qty';
                                                    $value = $child_arr[$field] ?? '';
                                                    ?>
                                                    <x-Inputs.text-field type="number" id="{{ $field }}-{{ $k }}" name="purchase_order_items[{{ $k }}][{{ $field }}]"
                                                        :label="$false" 
                                                        :value="$value"
                                                        class="will-required validate-int qty" />
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'uom_id';
                                                    $value = $child_arr[$field] ?? '';
                                                    ?>
                                                    <x-Inputs.drop-down id="{{ $field }}-{{ $k }}"
                                                        name="purchase_order_items[{{ $k }}][{{ $field }}]" 
                                                        :list="$empty_list" 
                                                        :label="$false" 
                                                        :value="$value"
                                                        class="will-required will-select2 uom_id" />
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'rate';
                                                    $value = $child_arr[$field] ?? '';
                                                    ?>
                                                    <x-Inputs.text-field id="{{ $field }}-{{ $k }}" name="purchase_order_items[{{ $k }}][{{ $field }}]"
                                                        :label="$false" 
                                                        :value="$value"
                                                        class="will-required validate-float rate" />
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'amount';
                                                    $value = $child_arr[$field] ?? '';
                                                    ?>
                                                    <x-Inputs.text-field id="{{ $field }}-{{ $k }}" name="purchase_order_items[{{ $k }}][{{ $field }}]"
                                                        :label="$false" 
                                                        :value="$value"
                                                        class="will-required validate-float amount" readonly="true"/>
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'remarks';
                                                    $value = $child_arr[$field] ?? '';
                                                    ?>
                                                    <x-Inputs.text-field name="purchase_order_items[{{ $k }}][{{ $field }}]" class="{{ $field }}"
                                                        :label="$false" 
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
                                    <span class="{{ $field }} big_amount">{{ $model->{$field} }}</span>
                                </div>
                            </div>

                            <?php
                            $field = 'advance_amount';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field layout="horizontal" :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}" 
                                class="validate-float"
                                horizontalSecondColumnLength="3"
                                placeholder="Enter {{ $label }}"
                                mandatory="true" 
                            />

                            <?php
                                $field = 'balance';
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
                                    <span class="{{ $field }} big_amount">{{ $model->{$field} }}</span>
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
        $(function() {
            var model_id = '<?= $model->id ?? 0 ?>';
            var currency_formatter = new Intl.NumberFormat('en-IN', { 
                style: 'currency', 
                currency: 'INR',
                maximumFractionDigits: 0
            });

            function cascade_item_id(_tr)
            {
                _tr.find(".item_id").cascade({
                    beforeGet: function (src, url) {
                        $.loader.init().setInfo("Fetching...").show();
                        return url;
                    },
                    afterGet: function (src, dest, response) {
                        $.loader.init().hide();
                        return response;
                    },
                });

                if (_tr.find(".item_id").val())
                {
                    _tr.find(".item_id").trigger("change");
                }
            }

            $(".template-table").srTableTemplate({
                brace_type: "(",
                afterRowAdd(_table, last_id, _tr) {
                    _tr.find(".will-select2").select2({
                        placeHolder: "Please Select",
                        theme: "bootstrap-5",
                    });

                    _tr.find(".will-required").attr("required", true);

                    cascade_item_id(_tr);
                }
            });

            var trs = $(".template-table").find(">tbody>tr").not(".sr-table-template-row");
            trs.each(function(index, _tr){
                cascade_item_id($(_tr), index);
            });

            function cal_amount(_tr)
            {
                var qty = _tr.find("input.qty").val();
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

                var advance_amount = $("#advance_amount").val();
                advance_amount = advance_amount ? parseFloat(advance_amount) : 0;

                var balance = total_amount - advance_amount;

                $("input.balance").val(balance);
                $("span.balance").html(currency_formatter.format(balance));
            }

            $(document).on("input keyup", ".qty, .rate", function(){
                cal_amount($(this).closest("tr"));
            });

            $(document).on("input keyup", "#advance_amount", cal_grand_amount);

            $.events.execAtLast(function() {

                cal_grand_amount();
            });
        });
    </script>
@endsection
