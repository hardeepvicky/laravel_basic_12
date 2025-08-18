<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-bordered summary-table summary_table_with_search">
                <thead>
                    <tr>
                        <th style="width:12%">
                            <span class="btn btn-light text-dark border-primary clear_search_conditions mb-1">Clear</span>
                            <?php
                            $field = "name";
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]"/>
                        </th>
                        <th style="width:20%">
                            <?php
                            $field = "parent_id";
                            ?>
                            <x-Inputs.drop-down :name="$field" :label="$false" :value="$search[$field]" :list="$tree_list" class="select2" />
                            <?php 
                                $field = "is_find_nested_child";
                            ?>
                            <x-Inputs.checkbox :name="$field" label="Find Nested Child" :value="$search[$field]" />
                        </th>
                        <th>
                            <?php
                            $field = "short_name";
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]"/>
                        </th>
                    </tr>
                    <tr class="border-bottom-primary">
                        <th style="width:10%">
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            | <?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?>
                        </th>
                        <th><?= sortable_anchor('parent_id', get_value_from_list($field_labels, 'parent_id', 'parent_id')) ?></th>
                        <th><?= sortable_anchor('short_name', get_value_from_list($field_labels, 'short_name', 'short_name')) ?></th>
                        <th><?= sortable_anchor('uom.name', get_value_from_list($field_labels, 'base_uom_id', 'base_uom_id')) ?></th>
                        <th style="width:8%">{{ get_value_from_list($field_labels, 'is_last_node', 'is_last_node') }}</th>
                        <th style="width:8%"><?= sortable_anchor('list_display_order', get_value_from_list($field_labels, 'list_display_order', 'list_display_order')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('is_active', 'Activate / De-Active') ?></th>
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
                                <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" buttons="edit,delete">
                                    <div class="label">
                                        {{ $record->id . " | " . $record->name }}
                                    </div>
                                </x-Backend.celec-summary-table-first-column>
                            </th>
                            <td>{{ get_value_from_list($tree_list, $record->parent_id) }}</td>
                            <td>{{ $record->short_name }}</td>
                            <td>{{ $record->uom?->getDisplayName() }}</td>
                            <td>{{ $record->is_last_node ? 'Last Node' : '' }}</td>
                            <td class="text-center">
                                <a class="x-editable" href="javascript:void();"id="list_display_order" data-type="number"
                                    data-name="list_display_order"
                                    data-pk="{{ $record->id }}"
                                    data-url="{{ route($routePrefix . '.ajax_update_field') }}"
                                    data-title="Enter Display Order">
                                    {{ $record->list_display_order }}
                                </a>
                            </td>
                            <td>
                                <x-Backend.active_deactive :isActive="$record->is_active" :id="$record->id" :routePrefix="$routePrefix" />
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
