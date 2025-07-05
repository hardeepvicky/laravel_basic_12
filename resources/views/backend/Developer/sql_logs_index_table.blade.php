<?php
$index_table_info_options['show_as'] = 'full_datetime';
?>
<div class="card" id="page-summary">
    <div class="card-header border-t-info">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr class="border-bottom-primary">
                        <th>
                            Route Name / Url
                            <hr>
                            <?= sortable_anchor('request_method', 'Request Method') ?>
                        </th>
                        <th>Cache Logs</th>
                        
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
                            <hr>
                            <label class="info">
                                Request Method : <span>{{ $record->request_method }}</span>
                            </label>
                        </td>
                        <td>
                            <div id="cache_logs-{{ $record->id }}" class="h-200">
                                {!! array_print($record->cacheLog?->logs ?? [], 200) !!}
                            </div>

                            <span class="btn btn-info btn-sm css-toggler mt-1 mb-1"
                                data-sr-css-class-toggle-target="#cache_logs-{{ $record->id }}"
                                data-sr-css-class-toggle-class="h-200" data-sr-css-class-toggle-applied-text="More"
                                data-sr-css-class-toggle-not-applied-text="Less">
                                More
                            </span>
                        </td>
                        <td>
                            <x-Backend.index-table-info :record="$record" :userList="$userListCache" :options="$index_table_info_options"/>
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