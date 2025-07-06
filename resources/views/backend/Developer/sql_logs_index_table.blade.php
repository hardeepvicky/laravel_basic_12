<div class="card" id="page-summary">
    <div class="card-header border-t-info">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr class="border-bottom-primary">
                        <th>Route Name / Url</th>
                        <th>
                            <?= sortable_anchor('request_method', 'Request Method') ?>
                        </th>
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
                            {{ $record->route_name_or_url }}
                        </td>
                        <td>{{ $record->request_method }}</td>
                        <td>
                            <x-Backend.index-table-info :record="$record" :userList="$userListCache"
                                :options="$index_table_info_options" />
                        </td>
                        <td>
                            @if ($record->sql_log_file_url)
                            <a href="{{ $record->sql_log_file_url }}" class="btn btn-outline-secondary  mb-2">
                                <i class="fas fa-download"></i>
                                SQL Log
                            </a>
                            @endif

                            @if ($record->sql_dml_log_file_url)
                            <a href="{{ $record->sql_dml_log_file_url }}" class="btn btn-outline-secondary  mb-2">
                                <i class="fas fa-download"></i>
                                DML SQL Log
                            </a>
                            @endif
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