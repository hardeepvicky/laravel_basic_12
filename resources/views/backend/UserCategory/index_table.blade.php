<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1 summary_table_with_search" data-base_url="{{ route($routePrefix . '.index') }}">
        <div class="table-responsive">
            <table class="table table-bordered summary-table">
                <thead>
                    <tr class="border-bottom-primary">
                        <th><?= sortable_anchor('id', get_value_from_list($field_labels, 'id')) ?></th>
                        <th><?= sortable_anchor('type', get_value_from_list($field_labels, 'type')) ?></th>
                        <th><?= sortable_anchor('name', get_value_from_list($field_labels, 'name')) ?></th>
                        <th><?= sortable_anchor('description', get_value_from_list($field_labels, 'description')) ?>
                        </th>
                        <th><?= get_value_from_list($field_labels, 'is_active') ?></th>
                        <th>Info</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                    <tr>
                        <th scope="row">{{ $record->id }}</th>
                        <td>{{ get_value_from_list($type_list, $record->type) }}</td>
                        <td>{{ $record->name }}</td>
                        <td>{{ $record->description }}</td>
                        <td>
                            <x-Backend.active_deactive :isActive="$record->is_active" :id="$record->id"
                                :routePrefix="$routePrefix" />
                        </td>
                        <td>
                            <x-Backend.index-table-info :record="$record" :userList="$userListCache" />
                        </td>
                        <td>
                            <x-Backend.summary-comman-actions :id="$record->id" :routePrefix="$routePrefix" />
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