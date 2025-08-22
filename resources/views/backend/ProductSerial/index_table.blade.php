<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="m-2">
            <a href="{{ route($routePrefix . ".export.csv") }}" class="btn btn-secondary">Export CSV</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered summary_table_with_search">
                <thead>
                    <tr>
                        <th style="width:15%">
                            <span class="btn btn-light text-dark border-primary clear_search_conditions mb-1">Clear</span>
                            <?php
                            $field = "serial_no";
                            ?>
                            <x-Inputs.text-field :id="$field"  :name="$field" :label="$false" :value="$search[$field]"/>
                        </th>
                        <th style="width:15%">
                            <?php
                            $field = 'product_category_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field"  :name="$field" :label="$label" :value="$search[$field]" :list="$product_category_list"
                                class="select2 cascade-drop-down"
                                data-sr-cascade-target="#product_id"
                                data-sr-cascade-url="/product-ajax_get_list?product_category_id={v}"
                                 />

                            <?php 
                                $field = "is_find_nested_child";
                            ?>
                            <x-Inputs.checkbox :id="$field"  :name="$field" label="Find Nested Child" :value="$search[$field]" />
                        </th>
                        <th style="width:15%">
                            <?php
                            $field = 'product_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :value="$search[$field]" :list="$product_list"
                                class="select2"
                                />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'make_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :value="$search[$field]" :list="$maker_list"
                                class="select2"
                                />
                        </th>
                    </tr>
                    <tr class="border-bottom-primary">
                        <th>
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            | <?= sortable_anchor('serial_no', get_value_from_list($field_labels, 'serial_no', 'serial_no')) ?>
                        </th>
                        <th><?= sortable_anchor('category.name', get_value_from_list($field_labels, 'product_category_id', 'product_category_id')) ?></th>
                        <th><?= sortable_anchor('product.name', get_value_from_list($field_labels, 'product_id', 'product_id')) ?></th>
                        <th><?= sortable_anchor('make.name', get_value_from_list($field_labels, 'make_id', 'make_id')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('created_at', get_value_from_list($field_labels, 'created_at', 'created_at')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('created_by', get_value_from_list($field_labels, 'created_by', 'created_by')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('updated_by', get_value_from_list($field_labels, 'updated_by', 'updated_by')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <th scope="row">
                                <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" buttons="edit,delete">
                                    <div class="label">
                                        {{ $record->id . " | " . $record->serial_no }}
                                    </div>
                                </x-Backend.celec-summary-table-first-column>
                            </th>
                            <td>{{ get_value_from_list($product_category_list, $record->product_category_id) }}</td>
                            <td>{{ $record->product?->getDisplayName() }}</td>
                            <td>{{ $record->maker?->getDisplayName() }}</td>
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