<div class="card" id="page-summary">
    <div class="card-header">
        <a href="{{ route($routePrefix . '.export_csv') }}" class="btn btn-secondary">Export CSV</a>
    </div>

    <div class="card-body p-1">        
        <div class="table-responsive">
            <table class="table table-bordered summary_table_with_search">
                <thead>
                    <tr>
                        <th style="width:10%">
                            <span class="btn btn-light btn-sm text-dark border-primary clear_search_conditions mb-1">Clear</span>
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
                        <th style="width:15%">
                            <?php
                            $field = 'make_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" :list="$make_list" class="select2 form-select-sm" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'branch_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" :list="$branch_list" class="select2 form-select-sm" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'bin_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" :list="$bin_list" class="select2 form-select-sm" />
                        </th>
                    </tr>
                    <tr class="border-bottom-primary">
                        <th>Sr.No.</th>
                        <th>Category</th>
                        <th><?= sortable_anchor('product.name', get_value_from_list($field_labels, 'product_id', 'product_id')) ?></th>
                        <th><?= sortable_anchor('make.name', get_value_from_list($field_labels, 'make_id', 'make_id')) ?></th>
                        <th><?= sortable_anchor('branch.name', get_value_from_list($field_labels, 'branch_id', 'branch_id')) ?></th>
                        <th><?= sortable_anchor('bin.name', get_value_from_list($field_labels, 'bin_id', 'bin_id')) ?></th>
                        <th style="width:8%">
                            <?= sortable_anchor('qty', get_value_from_list($field_labels, 'qty', 'qty')) ?>
                        </th>
                        <th style="width:10%">
                            <?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?>
                        </th>
                        <th style="width:10%">
                            <?= sortable_anchor('updated_by', get_value_from_list($field_labels, 'updated_by', 'updated_by')) ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $i => $record)
                        <tr>
                            <th scope="row">{{ $i + 1 }}</th>
                            <td>{{ get_value_from_list($product_category_list, $record->product->product_category_id) }}</td>
                            <td>{{ $record->product?->getDisplayName() }}</td>
                            <td>{{ $record->make?->getDisplayName() }}</td>
                            <td>{{ $record->branch?->getDisplayName() }}</td>
                            <td>{{ $record->bin?->getDisplayName() }}</td>
                            <td>{{ $record->qty }}</td>                            
                            <td>
                                <x-Backend.celec-date-time :value="$record->updated_at"/>
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
