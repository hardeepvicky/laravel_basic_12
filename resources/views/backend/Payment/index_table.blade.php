<?php
use App\Models\SaleOrder;
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
                                    $field = 'voucher_no';
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
                        <th style="width:10%">
                            <?php
                            $field = 'sale_order_no';
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm" />
                        </th>
                    </tr>
                    <tr class="border-bottom-primary">
                        <th>
                            <label class="form-check custom-checkbox-group">
                                <input type="checkbox" class="form-check-input chk-select-all" data-sr-chkselect-children="input.sale_order">
                            </label>
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            |
                            <?= sortable_anchor('voucher_no', get_value_from_list($field_labels, 'voucher_no', 'voucher_no')) ?>
                        </th>
                        <th><?= sortable_anchor('contact.name', get_value_from_list($field_labels, 'contact_id', 'contact_id')) ?></th>
                        <th>
                            <?= get_value_from_list($field_labels, 'sale_order_id', 'sale_order_id') ?>
                        </th>
                        <th style="width:8%">
                            <?= sortable_anchor('amount', get_value_from_list($field_labels, 'amount', 'amount')) ?>
                        </th>
                        <th style="width:8%">
                            <?= get_value_from_list($field_labels, 'remarks', 'remarks'); ?>
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
                                <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" buttons="edit,delete,tr_details">
                                    <div class="label">
                                        <label class="form-check custom-checkbox-group">                                            
                                            {{ $record->voucher_no }}
                                        </label>
                                    </div>
                                </x-Backend.celec-summary-table-first-column>
                            </th>
                            <td>
                                {{ $record->contact?->getDisplayName() }}
                            </td>
                            <td>
                                {{ $record->saleOrder?->getDisplayName() }}
                            </td>
                            <td>
                                {{ $formatter->formatCurrency($record->amount, "INR");  }}
                            </td>
                            <td>
                                {{ $record->remarks }}
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
                            <td colspan="5">
                                <x-Backend.document-files :records="$record->documentFile" />
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

<script>
    $(function(){

    });
</script>