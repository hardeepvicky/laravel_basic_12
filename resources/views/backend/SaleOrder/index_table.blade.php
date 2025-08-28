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
                            $field = 'contact_id';
                            ?>
                            <select id="{{ $field }}" name="{{ $field }}" class="form-select select2">
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
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            |
                            <?= sortable_anchor('order_no', get_value_from_list($field_labels, 'order_no', 'order_no')) ?>
                        </th>
                        <th><?= sortable_anchor('contact.name', get_value_from_list($field_labels, 'contact_id', 'contact_id')) ?></th>
                        <th style="width:10%">
                            <?= sortable_anchor('estimated_dispatch_date', get_value_from_list($field_labels, 'estimated_dispatch_date', 'estimated_dispatch_date')) ?>
                        </th>                       
                        <th style="width:8%">
                            <?= sortable_anchor('status', get_value_from_list($field_labels, 'status', 'status')) ?>
                        </th>
                        <th style="width:8%">
                            <?= sortable_anchor('payable_amount', get_value_from_list($field_labels, 'payable_amount', 'payable_amount')) ?>
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
                                        {{ $record->order_no }}
                                    </div>
                                </x-Backend.celec-summary-table-first-column>
                            </th>
                            <td>{{ $record->contact?->getDisplayName() }}</td>
                            <td>
                                {{ if_date($record->estimated_dispatch_date) }}
                            </td>
                            <td>
                                {{ get_value_from_list($status_list, $record->status) }}
                            </td>
                            <td>
                                {{ $formatter->formatCurrency($record->payable_amount, "INR");  }}
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
                                            {{ get_value_from_list($field_labels, 'party_order_ref_no') }} :
                                            <span>
                                                {{ $record->party_order_ref_no }}
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
                                                            <th>{{ get_value_from_list($sale_order_product_field_labels, 'product_id') }}</th>
                                                            <th>{{ get_value_from_list($sale_order_product_field_labels, 'make_id') }}</th>
                                                            <th>{{ get_value_from_list($sale_order_product_field_labels, 'order_qty') }}</th>
                                                            <th>{{ get_value_from_list($sale_order_product_field_labels, 'rate') }}</th>
                                                            <th>{{ get_value_from_list($sale_order_product_field_labels, 'amount') }}</th>
                                                            <th>{{ get_value_from_list($sale_order_product_field_labels, 'available_qty') }}</th>
                                                            <th>{{ get_value_from_list($sale_order_product_field_labels, 'qty_to_manufacture') }}</th>
                                                            <th>{{ get_value_from_list($sale_order_product_field_labels, 'remarks') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($record->saleOrderProduct as $i => $saleOrderProduct)
                                                            <tr>
                                                                <td>{{ $i + 1 }}</td>
                                                                <td>{{ $saleOrderProduct->product?->category->getDisplayName() }}</td>
                                                                <td>{{ $saleOrderProduct->product?->getDisplayName() }}</td>
                                                                <td>{{ $saleOrderProduct->make?->getDisplayName() }}</td>
                                                                <td>{{ $saleOrderProduct->order_qty }}</td>
                                                                <td>{{ $saleOrderProduct->rate }}</td>
                                                                <td>{{ $formatter->formatCurrency($saleOrderProduct->amount, "INR")  }}</td>
                                                                <td>{{ $saleOrderProduct->available_qty }}</td>
                                                                <td>{{ $saleOrderProduct->qty_to_manufacture }}</td>
                                                                <td>{{ $saleOrderProduct->remarks }}</td>
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
            </table>
        </div>
    </div>
    <div class="card-footer">
        <x-Backend.pagination-links :records="$records" />
    </div>
</div>
