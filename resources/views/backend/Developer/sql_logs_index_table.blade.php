<div class="card" id="page-summary">
    <div class="card-header border-t-info">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr class="border-bottom-primary">
                        <th>Route Name</th>
                        <th><?= sortable_anchor('request_method', 'Request Method') ?></th>
                        <th>URL</th>
                        <th>
                            <?= sortable_anchor('created_at', 'Created') ?>
                            | <?= sortable_anchor('updated_at', 'Updated') ?>
                        </th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                    <tr>
                        <td>
                            {{ $record->route_name }}
                        </td>
                        <td>{{ $record->request_method }}</td>
                        <td>{{ str_short_with_dot($record->url, 60) }}</td>
                        <td>
                            <x-Backend.index-table-info :record="$record" :userList="$userListCache"
                                :options="$index_table_info_options" />
                        </td>
                        <td>
                            @foreach($record->files as $file_arr)
                                @if($file_arr['file_url'])
                                    <a href="{{ $file_arr['file_url'] }}" class="btn btn-outline-secondary mb-2">
                                        <i class="fas fa-download"></i>
                                        {{ $file_arr['label'] }} 
                                        ({{ $file_arr['larvel_connection_key'] }} )
                                    </a>
                                @endif
                            @endforeach
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