<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr class="border-bottom-primary">
                        <th><?= sortable_anchor('id', 'ID') ?></th>
                        <th><?= sortable_anchor('type', 'Type') ?></th>
                        <th><?= sortable_anchor('name', 'Name') ?></th>
                        <th><?= sortable_anchor('description', 'Description') ?></th>
                        <th><?= sortable_anchor('is_active', 'Active / De-Active') ?></th>
                        <th><?= sortable_anchor('is_pre_defined', 'Pre-Defined') ?></th>
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
                                <x-Backend.active_deactive :isActive="$record->is_active" :id="$record->id" :routePrefix="$routePrefix" />
                            </td>
                            <td>
                                <x-Backend.yes-no-label :value="$record->is_pre_defined" />
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
