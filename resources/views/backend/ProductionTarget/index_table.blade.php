<?php

use App\Models\User;

?>
<div class="card" id="page-summary">
    <div class="card-header border-t-info">
        <a href="{{ route($routePrefix . '.export.csv') }}" class="btn btn-secondary">Export CSV</a>
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-bordered summary-table summary_table_with_search">
                <thead>
                    <tr>
                        <th style="width:10%">
                            <span class="btn btn-light btn-sm text-dark border-primary clear_search_conditions mb-1">Clear</span>
                        </th>
                        <th></th>
                        <th style="width:10%">
                            <?php
                            $field = "branch_id";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" :list="$branch_list" :value="$search[$field]"
                                pleaseSelect="All" class="select2 form-select-sm"  />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = "responsible_user_id";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" :list="$user_list" :value="$search[$field]"
                                pleaseSelect="All" class="select2 form-select-sm"  />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = "product_id";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" :list="$product_list" :value="$search[$field]"
                                pleaseSelect="All" class="select2 form-select-sm"  />
                        </th>
                        <th></th>
                        <th style="width:8%">
                            <?php
                            $field = "target_date";
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm date-picker"/>
                        </th>                        
                    </tr>
                    <tr class="border-bottom-primary">
                        <th style="width:10%">
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            | <?= sortable_anchor('name', get_value_from_list($field_labels, 'full_name', 'full_name')) ?>
                        </th>
                        <th>Actions</th>
                        <th><?= sortable_anchor('branch.name', get_value_from_list($field_labels, 'branch_id', 'branch_id')) ?></th>
                        <th><?= sortable_anchor('user.name', get_value_from_list($field_labels, 'responsible_user_id', 'responsible_user_id')) ?></th>
                        <th><?= sortable_anchor('product.name', get_value_from_list($field_labels, 'product_id', 'product_id')) ?></th>
                        <th><?= sortable_anchor('target_date', get_value_from_list($field_labels, 'target_date', 'target_date')) ?></th>
                        <th style="width:8%"><?= get_value_from_list($field_labels, 'qty', 'qty') ?></th>
                        <th style="width:8%"><?= get_value_from_list($field_labels, 'complete_qty', 'complete_qty') ?></th>
                        <th style="width:8%"><?= sortable_anchor('created_at', get_value_from_list($field_labels, 'created_at', 'created_at')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                    <tr>
                        <th scope="row">
                            <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" buttons="edit,delete,tr_details">
                                <div class="label">
                                    {{ $record->id}}
                                </div>
                            </x-Backend.celec-summary-table-first-column>
                        </th>
                        <td>
                            <a class="btn btn-sm btn-secondary mb-2" href="{{ route($routePrefix . '.completion', [$record->id]) }}">Add Complete</a>
                        </td>
                        <td>{{ $record->branch?->getDisplayName() }}</td>
                        <td>{{ $record->user?->getDisplayName() }}</td>
                        <td>{{ $record->product?->getDisplayName() }}</td>
                        <td>{{ if_date($record->target_date) }}</td>
                        <td>{{ $record->qty }}</td>
                        <td>{{ $record->complete_qty }}</td>
                        <td>
                            <x-Backend.created-and-by :datetime="$record->created_at" :userId="$record->created_by" />
                        </td>
                        <td>
                            <x-Backend.created-and-by :datetime="$record->updated_at" :userId="$record->updated_by" />
                        </td>
                    </tr>
                    <tr id="record-{{ $record->id }}" class="hidden">
                        <td style="background-color: var(--body--bs-body-bg)"></td>
                        <td colspan="10">
                            <div class="row">
                                <div class="col-md-6 col-xxl-4">
                                    <label class="info">
                                        {{ get_value_from_list($field_labels, 'remarks') }} :
                                        <span>
                                            {{ $record->remarks }}
                                        </span>
                                    </label>
                                </div>

                                <div class="col-md-6 col-xxl-8">
                                    <div class="card">
                                        <div class="card-header border-l-primary card-no-border">
                                            <h3>Completions</h3>
                                        </div>
                                        <div class="card-body">                                            
                                            <table class="table table-sm summary-table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Sr. No.</th>
                                                        <th>{{ get_value_from_list($production_target_complete_fields, 'complete_by_employee_id') }}</th>
                                                        <th>{{ get_value_from_list($production_target_complete_fields, 'qty') }}</th>
                                                        <th>{{ get_value_from_list($production_target_complete_fields, 'remarks') }}</th>
                                                        <th>
                                                            Actions
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($record->productionTarget as $i => $productionTarget)
                                                    <tr>
                                                        <td>{{ $i + 1 }}</td>
                                                        <td>{{ $productionTarget->employee?->getDisplayName() }}</td>
                                                        <td>{{ $productionTarget->qty }}</td>
                                                        <td>{{ $productionTarget->remarks }}</td>
                                                        <td>
                                                            <x-Backend.summary-delete-button url="{{ route($routePrefix . '.completion_destroy', [$productionTarget->id]) }}" />
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