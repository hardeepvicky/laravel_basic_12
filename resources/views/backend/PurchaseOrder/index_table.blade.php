<?php
use App\Models\PurchaseOrder;
$formatter = new NumberFormatter('en_IN', NumberFormatter::CURRENCY);
$formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);
?>
<div class="card" id="page-summary">
    <div class="card-header">
        <a href="{{ route($routePrefix . '.export.csv') }}" class="btn btn-secondary">Export CSV</a>
        <a href="{{ route($routePrefix . '.export.item_csv') }}" class="btn btn-secondary">Export Item CSV</a>
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
                        <th></th>
                        <th style="width:10%">
                            <?php
                            $field = 'branch_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]"
                                :list="$branch_list"
                                class="select2 form-select-sm"
                            />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'party_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]"
                                :list="$party_list"
                                class="select2 form-select-sm"
                            />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'status';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]"
                                :list="$status_list"
                                class="select2 form-select-sm"
                            />
                        </th>
                    </tr>
                    <tr class="border-bottom-primary">
                        <th>
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            |
                            <?= sortable_anchor('order_no', get_value_from_list($field_labels, 'order_no', 'order_no')) ?>
                        </th>
                        <th style="width:8%"> Actions </th>
                        <th><?= sortable_anchor('branch.name', get_value_from_list($field_labels, 'branch_id', 'branch_id')) ?></th>
                        <th><?= sortable_anchor('party.name', get_value_from_list($field_labels, 'party_id', 'party_id')) ?></th>
                        <th style="width:8%">
                            <?= sortable_anchor('status', get_value_from_list($field_labels, 'status', 'status')) ?>
                        </th>
                        <th style="width:8%">
                            <?= sortable_anchor('total_amount', get_value_from_list($field_labels, 'total_amount', 'total_amount')) ?>
                        </th>
                        <th style="width:8%">
                            <?= sortable_anchor('advance_amount', get_value_from_list($field_labels, 'advance_amount', 'advance_amount')) ?>
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
                            <td>
                                @if(in_array($record->status, [PurchaseOrder::STATUS_APPROVE, PurchaseOrder::STATUS_RECEIVED]))
                                    <a class="btn btn-sm btn-secondary mb-2" href="{{ route('purchase-order-payment.create', ["purchase_order_id" => $record->id]) }}">Pay Now</a>
                                @endif
                                @if($record->status == PurchaseOrder::STATUS_APPROVE)
                                    <a class="btn btn-sm btn-secondary mb-2" href="{{ route($routePrefix . '.receive_items', [$record->id]) }}">Receive Items</a>
                                @endif
                            </td>
                            <td>
                                {{ $record->branch?->getDisplayName() }}
                            </td>
                            <td>
                                {{ $record->party?->getDisplayName() }}
                            </td>
                            <td>
                                {{ get_value_from_list($status_list, $record->status) }}

                                <div class="mt-1">
                                    @if($record->status == PurchaseOrder::STATUS_PENDING || $record->status == PurchaseOrder::STATUS_DECLINE)
                                        <a class="btn btn-sm btn-secondary confirm" href="{{ route($routePrefix . '.approve', [$record->id]) }}">Approve</a>
                                    @elseif($record->status == PurchaseOrder::STATUS_APPROVE)
                                        <a class="btn btn-sm btn-danger confirm" href="{{ route($routePrefix . '.decline', [$record->id]) }}">Decline</a>
                                    @endif
                                </div>
                            </td>
                            <td>
                                {{ $formatter->formatCurrency($record->total_amount, "INR");  }}
                            </td>
                            <td>
                                {{ $formatter->formatCurrency($record->advance_amount, "INR");  }}
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
                                                <h3>Items</h3>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm summary-table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>Category</th>
                                                            <th>{{ get_value_from_list($purchase_order_item_field_labels, 'item_id') }}</th>
                                                            <th>{{ get_value_from_list($purchase_order_item_field_labels, 'make_id') }}</th>
                                                            <th>{{ get_value_from_list($purchase_order_item_field_labels, 'qty') }}</th>
                                                            <th>{{ get_value_from_list($purchase_order_item_field_labels, 'receive_qty') }}</th>
                                                            <th>{{ get_value_from_list($purchase_order_item_field_labels, 'rate') }}</th>
                                                            <th>{{ get_value_from_list($purchase_order_item_field_labels, 'amount') }}</th>
                                                            <th>{{ get_value_from_list($purchase_order_item_field_labels, 'remarks') }}</th>
                                                            <th>
                                                                Actions
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($record->purchaseOrderItem as $i => $purchaseOrderItem)
                                                            <tr>
                                                                <td>{{ $i + 1 }}</td>
                                                                <td>{{ $purchaseOrderItem->item?->category?->getDisplayName() }}</td>
                                                                <td>{{ $purchaseOrderItem->item?->getDisplayName() }}</td>
                                                                <td>{{ $purchaseOrderItem->make?->getDisplayName() }}</td>
                                                                <td>
                                                                    {{ $purchaseOrderItem->qty }}

                                                                    {{ $purchaseOrderItem->item?->category?->uom?->getDisplayName() }}
                                                                </td>
                                                                <td>
                                                                    {{ $purchaseOrderItem->receive_qty }}

                                                                    {{ $purchaseOrderItem->item?->category?->uom?->getDisplayName() }}
                                                                </td>
                                                                <td>{{ $purchaseOrderItem->rate }}</td>
                                                                <td>{{ $formatter->formatCurrency($purchaseOrderItem->amount, "INR")  }}</td>
                                                                <td>{{ $purchaseOrderItem->remarks }}</td>
                                                                <td>
                                                                    <span class="btn btn-outline-light text-dark summary-action-button css-toggler"
                                                                        data-sr-css-class-toggle-target="#poi-{{ $purchaseOrderItem->id }}"
                                                                        data-sr-css-class-toggle-class="hidden">
                                                                        <i class="fa-solid fa-circle-info"></i>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr id="poi-{{ $purchaseOrderItem->id }}" class="hidden">
                                                                <td style="background-color: var(--body--bs-body-bg)"></td>
                                                                <td colspan="8">
                                                                    <table class="table table-sm summary-table table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Sr. No.</th>
                                                                                <th>{{ get_value_from_list($purchase_order_item_field_labels, 'receive_qty') }}</th>
                                                                                <th>{{ get_value_from_list($purchase_order_item_field_labels, 'remarks') }}</th>
                                                                                <th>
                                                                                    Actions
                                                                                </th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($purchaseOrderItem?->purchaseOrderReceiveItem as $a => $purchaseOrderReceiveItem)
                                                                            <tr>
                                                                                <td>{{ $a + 1 }}</td>
                                                                                <td>{{ $purchaseOrderReceiveItem->receive_qty }}</td>
                                                                                <td>{{ $purchaseOrderReceiveItem->remarks }}</td>
                                                                                <td>
                                                                                    <x-Backend.summary-delete-button url="{{ route($routePrefix . '.purchase_order_receive_item_destroy', [$purchaseOrderReceiveItem->id]) }}" />
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </td>
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