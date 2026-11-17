<div class="card" id="page-summary">
    <div class="card-header border-t-info">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr class="border-bottom-primary">
                        <th style="width: 20%">
                            <?= sortable_anchor('route_name', 'Route Name') ?>
                            | <?= sortable_anchor('request_method', 'Request Method') ?>
                        </th>
                        <th><?= sortable_anchor('sql_query_count', 'ALL SQL Query Count') ?></th>
                        <th><?= sortable_anchor('dml_query_count', 'DML Query Count') ?></th>
                        <th><?= sortable_anchor('heavy_query_count', 'Heavy Query Count') ?></th>
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

                            <label class="info">
                                Request Method : <span>{{ $record->request_method }}</span>
                            </label>

                            <div id="url-{{ $record->id }}" class="mx-h-200 w-300">
                                <label class="info">
                                    Url : <span>{{ str_short_with_dot($record->url, 180) }}</span>
                                </label>
                            </div>

                            <span class="btn btn-info btn-sm css-toggler mt-1 mb-1"
                                data-sr-css-class-toggle-target="#url-{{ $record->id }}"
                                data-sr-css-class-toggle-class="mx-h-200" data-sr-css-class-toggle-applied-text="More"
                                data-sr-css-class-toggle-not-applied-text="Less">
                                More
                            </span>
                        </td>
                        <td>
                            {{ $record->sql_query_count }}
                        </td>
                        <td>
                            {{ $record->dml_query_count }}
                        </td>
                        <td>
                            {{ $record->heavy_query_count }}
                        </td>
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