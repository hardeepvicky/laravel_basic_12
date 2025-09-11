<?php

$formatter = new NumberFormatter('en_IN', NumberFormatter::CURRENCY);
$formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);
?>
<div class="card" id="page-summary">
    <div class="card-header">
        <a href="{{ route($routePrefix . '.export.csv') }}" class="btn btn-secondary">Export CSV</a>
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-bordered summary_table_with_search">
                <thead>
                    <tr>
                        <th style="width:10%">
                            <div class="row">
                                <div class="col-sm-4">
                                    <span class="btn btn-light btn-sm text-dark border-primary clear_search_conditions mb-1">Clear</span>
                                </div>
                                <div class="col-sm-8">
                                    <?php
                                    $field = 'order_no';
                                    ?>
                                    <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm" />
                                </div>
                            </div>
                        </th>
                        <th style="width:25%">
                            <?php
                            $field = 'export_contact_id';
                            ?>
                            <select id="{{ $field }}" name="{{ $field }}" class="form-select select2 form-select-sm">
                                <option value="">Please Select</option>
                                @foreach ($contact_list as $k => $t)
                                    @php
                                        $attr = $k == $search[$field] ? 'selected="selected"' : '';
                                    @endphp
                                    <option value="{{ $k }}" {!! $attr !!}>
                                        {{ $t['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </th>
                    </tr>
                    <tr class="border-bottom-primary">
                        <th>
                            <label class="form-check custom-checkbox-group">
                                <input type="checkbox" class="form-check-input chk-select-all" data-sr-chkselect-children="input.export_order">
                            </label>
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            |
                            <?= sortable_anchor('order_no', get_value_from_list($field_labels, 'order_no', 'order_no')) ?>
                        </th>
                        <th><?= sortable_anchor('contact.name', get_value_from_list($field_labels, 'export_contact_id', 'export_contact_id')) ?></th>
                        <th style="width:8%">
                            <?= sortable_anchor('status', get_value_from_list($field_labels, 'status', 'status')) ?>
                        </th>
                        <th style="width:8%">
                            <?= sortable_anchor('total_amount', get_value_from_list($field_labels, 'total_amount', 'total_amount')) ?>
                        </th>
                        <th style="width:8%">
                            <?= sortable_anchor('paid_amount', get_value_from_list($field_labels, 'paid_amount', 'paid_amount')) ?>
                        </th>
                        <th style="width:8%">
                            <?= sortable_anchor('created_at', get_value_from_list($field_labels, 'created_at', 'created_at')) ?>
                        </th>
                        <th style="width:8%">
                            <?= sortable_anchor('created_by', get_value_from_list($field_labels, 'created_by', 'created_by')) ?>
                        </th>
                        <th style="width:8%">
                            <?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?>
                        </th>
                        <th style="width:8%">
                            <?= sortable_anchor('updated_by', get_value_from_list($field_labels, 'updated_by', 'updated_by')) ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <th scope="row">
                                <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" buttons="copy,edit,delete,tr_details">
                                    <div class="label">
                                        @if(!$record->isFullySent())
                                                <input type="checkbox" class="form-check-input export_order" value={{ $record->id }}>
                                            @endif
                                        {{ $record->order_no }}
                                    </div>
                                </x-Backend.celec-summary-table-first-column>
                            </th>
                            <td>
                                @if(strtoupper($record->order_from_type) == "LEAD")
                                    Lead >> {{ $record->customer?->name }} >> {{ $record->customer?->city?->getDisplayName() }}
                                @else
                                    {{ $record->contact?->getDisplayName() }}
                                @endif
                            </td>
                            <td>
                                {{ get_value_from_list($status_list, $record->status) }}
                            </td>
                            <td>
                                {{ $formatter->formatCurrency($record->total_amount, "INR");  }}
                            </td>
                            <td>
                                {{ $formatter->formatCurrency($record->paid_amount, "INR");  }}
                            </td>
                            <td>
                                <x-Backend.celec-date-time :value="$record->created_at" />
                            </td>
                            <td>
                                {{ get_value_from_list($userListCache, $record->created_by) }}
                            </td>
                            <td>
                                <x-Backend.celec-date-time :value="$record->updated_at" :userId="$record->updated_by" />
                            </td>
                            <td>
                                {{ get_value_from_list($userListCache, $record->updated_by) }}
                            </td>
                        </tr>
                        <tr id="record-{{ $record->id }}" class="hidden">
                            <td style="background-color: var(--body--bs-body-bg)"></td>
                            <td colspan="9">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'remarks') }} :
                                            <span>
                                                {{ $record->remarks }}
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="card">
                                            <div class="card-header border-l-secondary card-no-border">
                                                <h3>Products</h3>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm summary-table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>Category</th>
                                                            <th>{{ get_value_from_list($export_order_product_field_labels, 'product_id') }}</th>
                                                            <th>{{ get_value_from_list($export_order_product_field_labels, 'make_id') }}</th>
                                                            <th>{{ get_value_from_list($export_order_product_field_labels, 'order_qty') }}</th>
                                                            <th>{{ get_value_from_list($export_order_product_field_labels, 'rate') }}</th>
                                                            <th>{{ get_value_from_list($export_order_product_field_labels, 'amount') }}</th>
                                                            <th>{{ get_value_from_list($export_order_product_field_labels, 'available_qty') }}</th>
                                                            <th>{{ get_value_from_list($export_order_product_field_labels, 'qty_to_manufacture') }}</th>
                                                            <th>{{ get_value_from_list($export_order_product_field_labels, 'remarks') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($record->exportOrderProduct as $i => $exportOrderProduct)
                                                            <tr>
                                                                <td>{{ $i + 1 }}</td>
                                                                <td>{{ $exportOrderProduct->product?->category->getDisplayName() }}</td>
                                                                <td>{{ $exportOrderProduct->product?->getDisplayName() }}</td>
                                                                <td>{{ $exportOrderProduct->make?->getDisplayName() }}</td>
                                                                <td>{{ $exportOrderProduct->order_qty }}</td>
                                                                <td>{{ $exportOrderProduct->rate }}</td>
                                                                <td>{{ $formatter->formatCurrency($exportOrderProduct->amount, "INR")  }}</td>
                                                                <td>{{ $exportOrderProduct->available_qty }}</td>
                                                                <td>{{ $exportOrderProduct->qty_to_manufacture }}</td>
                                                                <td>{{ $exportOrderProduct->remarks }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            <span id="create_ship_order" class="btn btn-secondary btn-sm mb-1">Create Ship Order</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <x-Backend.pagination-links :records="$records" />
    </div>
</div>


<script>
    $(function(){
        $("#create_ship_order").click(function(){
            var id_list = [];

            $("input.export_order:checked").each(function(){
                var v = $(this).val();

                id_list.push(v);
            });

            if (id_list.length <= 0)
            {
                $.events.onUserError("Please Select at least one Order");
                return false;
            }

            var params = [
                {
                    name : "_token", 
                    value : '{{ csrf_token() }}'
                },
                {
                    name : "_method", 
                    value : 'POST'
                },
            ];

            for(var i in id_list)
            {
                params.push({
                    name : "export_order_ids[]",
                    value : id_list[i]
                });
            }

            add_form_to_body_and_submit("post", "/ship-order/create_from_export_order", params);
        });
    });
</script>