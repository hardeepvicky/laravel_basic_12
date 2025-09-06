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
                                    $field = 'serial_no';
                                    ?>
                                    <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm" />
                                </div>
                            </div>
                        </th>
                        <th style="width:15%">
                            <?php
                            $field = 'product_category_id';
                            ?>
                            <x-Inputs.drop-down :name="$field" :label="$false" :value="$search[$field]" :list="$product_category_list"
                                class="select2 form-select-sm" />
                        </th>
                        <th style="width:15%">
                            <?php
                            $field = 'product_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" :list="$product_list" class="select2 form-select-sm" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'make_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" :list="$maker_list" class="select2 form-select-sm" />
                        </th>
                    </tr>
                    <tr class="border-bottom-primary">
                        <th>
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            |
                            <?= sortable_anchor('serial_no', get_value_from_list($field_labels, 'serial_no', 'serial_no')) ?>
                        </th>
                        <th>Category</th>
                        <th><?= sortable_anchor('product.name', get_value_from_list($field_labels, 'product_id', 'product_id')) ?></th>
                        <th><?= sortable_anchor('make.name', get_value_from_list($field_labels, 'make_id', 'make_id')) ?></th>
                        <th style="width:10%">
                            <?= sortable_anchor('hierarchy_type', get_value_from_list($field_labels, 'hierarchy_type', 'hierarchy_type')) ?>
                        </th>
                        <th style="width:10%">
                            <?= sortable_anchor('manufacture_date_time', get_value_from_list($field_labels, 'manufacture_date_time', 'manufacture_date_time')) ?>
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
                                <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix"
                                    buttons="edit,delete">
                                    <div class="label">
                                        {{ $record->serial_no }}
                                    </div>
                                </x-Backend.celec-summary-table-first-column>
                            </th>
                            <td>{{ $record->product->category?->getDisplayName() }}</td>
                            <td>{{ $record->product?->getDisplayName() }}</td>
                            <td>{{ $record->maker?->getDisplayName() }}</td>
                            <td>{{ get_value_from_list($type_list, $record->hierarchy_type) }}</td>
                            <td>
                                <x-Backend.celec-date-time :value="$record->manufacture_date_time" />
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <x-Backend.pagination-links :records="$records" />
    </div>
</div>
