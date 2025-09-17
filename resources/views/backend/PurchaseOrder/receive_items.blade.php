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
                            <?php
                            $field = 'order_no';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <div class="row">
                                <div class="col-sm-3">
                                    {{ $label }}
                                </div>
                                <div class="col-sm-3">
                                    {{ $model->{$field} }}
                                </div>
                            </div>
                            <?php
                            $field = 'branch_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <div class="row">
                                <div class="col-sm-3">
                                    {{ $label }}
                                </div>
                                <div class="col-sm-3">
                                    {{ $model->branch->getDisplayName() }}
                                </div>
                            </div>
                            <?php
                            $field = 'party_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <div class="row">
                                <div class="col-sm-3">
                                    {{ $label }}
                                </div>
                                <div class="col-sm-3">
                                    {{ $model->party->getDisplayName() }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 col-xxl-12">
                            <table class="table table-sm summary-table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Category</th>
                                        <th>{{ get_value_from_list($purchase_order_item_field_labels, 'item_id') }}</th>
                                        <th>{{ get_value_from_list($purchase_order_item_field_labels, 'make_id') }}</th>
                                        <th>{{ get_value_from_list($purchase_order_item_field_labels, 'qty') }}</th>
                                        <th>{{ get_value_from_list($purchase_order_item_field_labels, 'rate') }}</th>
                                        <th>Received Till Now</th>
                                        <th>{{ get_value_from_list($purchase_order_item_field_labels, 'receive_qty') }}</th>
                                        <th>{{ get_value_from_list($purchase_order_item_field_labels, 'remarks') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($model->purchaseOrderItem as $i => $purchaseOrderItem)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $purchaseOrderItem->item?->category->getDisplayName() }}</td>
                                            <td>{{ $purchaseOrderItem->item?->getDisplayName() }}</td>
                                            <td>{{ $purchaseOrderItem->make?->getDisplayName() }}</td>
                                            <td>
                                                {{ $purchaseOrderItem->qty }}

                                                {{ $purchaseOrderItem->item?->category?->uom?->getDisplayName() }}
                                            </td>
                                            <td>
                                                <span class="qty">
                                                    {{ $purchaseOrderItem->qty }}
                                                </span>

                                                {{ $purchaseOrderItem->item?->category?->uom?->getDisplayName() }}
                                            </td>
                                            <td>
                                                <span class="receive_qty">
                                                    {{ $purchaseOrderItem->receive_qty }}
                                                </span>

                                                {{ $purchaseOrderItem->item?->category?->uom?->getDisplayName() }}
                                            </td>
                                            <td>
                                                <x-inputs.text-field type="hidden" name="purchase_order_receive_items[{{ $i }}][purchase_order_item_id]" :value="$purchaseOrderItem->id" label="" />
                                                <?php
                                                $field = 'receive_qty';                                                
                                                ?>
                                                <x-Inputs.text-field type="number" id="{{ $field }}-(sr-id)" name="purchase_order_receive_items[{{ $i }}][{{ $field }}]"
                                                    :label="$false"
                                                    :mandatory="true"
                                                    class="validate-int receive_qty" 
                                                />
                                            </td>
                                            <td>
                                                <?php
                                                $field = 'remarks';
                                                ?>
                                                <x-Inputs.text-field name="purchase_order_receive_items[{{ $i }}][{{ $field }}]" :label="$false" />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-buttons mb-4">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
    </form>

    <script>
        $(function(){

            $("form").submit(function(){
                var _this = $(this);

                var will_submit = true;

                $("input.receive_qty").each(function(){
                    var _tr = $(this).closest("tr");

                    var input_qty = $(this).val();
                    input_qty = input_qty ? parseFloat(input_qty) : 0;

                    var qty = _tr.find(".qty").text();
                    qty = qty ? parseFloat(qty) : 0;

                    var receive_qty = _tr.find(".qty").text();
                    receive_qty = receive_qty ? parseFloat(qty) : 0;

                    if (input_qty > qty - receive_qty)
                    {
                        will_submit = false;
                    }
                })

                if (!will_submit)
                {
                    var confirm = $(this).attr("data-confirm");

                    if (!confirm)
                    {
                        confirmDialog("Some Receive Qty is greater than Qty.", function(){
                            _this.attr("data-confirm", 1);
                            _this.trigger("submit");
                        });

                        return false;
                    }
                }
            });
        });
    </script>

@endsection