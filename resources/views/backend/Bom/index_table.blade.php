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
                            <span class="btn btn-light btn-sm text-dark border-primary clear_search_conditions mb-1">Clear</span>                            
                        </th>
                        <th style="width:8%">
                            <?php
                            $field = "type";
                            ?>
                            <x-Inputs.drop-down :name="$field" :label="$false" :value="$search[$field]" :list="$type_list" class="select2" pleaseSelect="All" class="select2 form-select-sm"/>
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = "item_id";
                            ?>
                            <x-Inputs.drop-down :name="$field" :label="$false" :value="$search[$field]" :list="$item_list" class="select2" pleaseSelect="All" class="select2 form-select-sm"/>
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = "product_id";
                            ?>
                            <x-Inputs.drop-down :name="$field" :label="$false" :value="$search[$field]" :list="$product_list" class="select2" pleaseSelect="All" class="select2 form-select-sm"/>
                        </th>                        
                    </tr>
                    <tr class="border-bottom-primary">
                        <th>
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            | <?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?>
                        </th>
                        <th style="width:10%">{{ get_value_from_list($field_labels, 'type', 'type') }}</th>
                        <th>{{ get_value_from_list($field_labels, 'item_id', 'item_id') }}</th>
                        <th>{{ get_value_from_list($field_labels, 'product_id', 'product_id') }}</th>                        
                        <th style="width:10%"><?= sortable_anchor('created_at', get_value_from_list($field_labels, 'created_at', 'created_at')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('created_by', get_value_from_list($field_labels, 'created_by', 'created_by')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('updated_by', get_value_from_list($field_labels, 'updated_by', 'updated_by')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <th scope="row">
                                <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" buttons="edit,delete,tr_details">
                                    <div class="label">
                                        {{ $record->id }}
                                    </div>
                                </x-Backend.celec-summary-table-first-column>
                            </th>                            
                            <td>{{ get_value_from_list($type_list, $record->type) }}</td>
                            <td>{{ $record->item?->getDisplayName() }}</td>
                            <td>{{ $record->product?->getDisplayName() }}</td>                            
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
                            <td colspan="6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'remarks') }} :
                                            <span>
                                                {{ $record->remarks }}
                                            </span>
                                        </label>
                                        
                                        <div class="card">
                                            <div class="card-header border-l-secondary card-no-border">
                                                <h3>Items</h3>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm summary-table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>Item</th>
                                                            <th>Qty</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($record->bomItems as $i => $bomItems)
                                                            <tr>
                                                                <td>{{ $i + 1 }}</td>
                                                                <td>{{ $bomItems->item?->getDisplayName() }}</td>
                                                                <td>
                                                                    {{ $bomItems->qty }}
                                                                    {{ $bomItems->item?->category?->uom?->getDisplayName() }}
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
