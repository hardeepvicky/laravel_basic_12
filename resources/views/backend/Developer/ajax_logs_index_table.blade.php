<?php

use App\Helpers\Util;
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
                            Route Name
                            <hr>
                            <?= sortable_anchor('request_method', 'Request Method') ?>
                        </th>
                        <th><?= sortable_anchor('exec_time', 'Time Taken (Seconds)') ?></th>
                        <th>Request</th>
                        <th>Response</th>
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

                            <label class="info">
                                Request Method : <span>{{ $record->request_method }}</span>
                            </label>

                            <div id="url-{{ $record->id }}" class="h-200">
                                <label class="info">
                                    Url : <span>{{ str_short_with_dot($record->url, 180) }}</span>
                                </label>
                            </div>
                            
                            <span class="btn btn-info btn-sm css-toggler mt-1 mb-1"
                                    data-sr-css-class-toggle-target="#url-{{ $record->id }}"
                                    data-sr-css-class-toggle-class="h-200" data-sr-css-class-toggle-applied-text="More"
                                    data-sr-css-class-toggle-not-applied-text="Less">
                                    More
                            </span>
                        </td>
                        <td>{{ Util::niceTime($record->exec_time) }}</td>
                        <td>
                            <div id="request-{{ $record->id }}" class="h-200">
                                {!! array_print($record->request, 100) !!}
                            </div>

                            <span class="btn btn-info btn-sm css-toggler mt-1 mb-1"
                                data-sr-css-class-toggle-target="#request-{{ $record->id }}"
                                data-sr-css-class-toggle-class="h-200" data-sr-css-class-toggle-applied-text="More"
                                data-sr-css-class-toggle-not-applied-text="Less">
                                More
                            </span>
                        </td>
                        <td>
                            <div id="response-{{ $record->id }}" class="h-200">
                                {!! array_print($record->response, 100) !!}
                            </div>

                            <span class="btn btn-info btn-sm css-toggler mt-1 mb-1"
                                data-sr-css-class-toggle-target="#response-{{ $record->id }}"
                                data-sr-css-class-toggle-class="h-200" data-sr-css-class-toggle-applied-text="More"
                                data-sr-css-class-toggle-not-applied-text="Less">
                                More
                            </span>
                        </td>
                        <td>
                            <div id="cache_logs-{{ $record->id }}" class="h-200">
                                {!! array_print($record->cacheLog?->logs ?? [], 100) !!}
                            </div>

                            <span class="btn btn-info btn-sm css-toggler mt-1 mb-1"
                                data-sr-css-class-toggle-target="#cache_logs-{{ $record->id }}"
                                data-sr-css-class-toggle-class="h-200" data-sr-css-class-toggle-applied-text="More"
                                data-sr-css-class-toggle-not-applied-text="Less">
                                More
                            </span>
                        </td>
                        <td>
                            <x-Backend.index-table-info :record="$record" :userList="$userListCache" />
                        </td>
                        <td>
                            <div class="mt-2 mb-2">
                                @if (isset($record->sql_model['sql_log_file_url']))
                                <a href="{{ $record->sql_model['sql_log_file_url'] }}"
                                    class="btn btn-outline-secondary mb-2">
                                    <i class="fas fa-download"></i>
                                    SQL Log
                                </a>
                                @endif

                                @if (isset($record->sql_model['sql_dml_log_file_url']))
                                <a href="{{ $record->sql_model['sql_dml_log_file_url'] }}"
                                    class="btn btn-outline-secondary mb-2">
                                    <i class="fas fa-download"></i>
                                    DML SQL Log
                                </a>
                            </div>
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