<?php

use App\Helpers\Util;
?>
<div class="card" id="page-summary">
    <div class="card-header border-t-info">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-bordered summary-table">
                <thead>
                    <tr class="border-bottom-primary">
                        <th>Command</th>
                        <th>Description</th>
                        <th><?= sortable_anchor('exec_time', 'Time Taken (Seconds)') ?></th>
                        <th>Info</th>
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
                        <td>{{ $record->command }}</td>
                        <td>{{ $record->description }}</td>
                        <td>{{ Util::niceTime($record->exec_time) }}</td>
                        <td>
                            @if(isset($record->info) && is_array($record->info))
                            <div id="info-{{ $record->id }}" class="h-200">
                                {!! array_print($record->info) !!}
                            </div>

                            <span class="btn btn-info btn-sm css-toggler mt-1 mb-1"
                                data-sr-css-class-toggle-target="#info-{{ $record->id }}"
                                data-sr-css-class-toggle-class="h-200" data-sr-css-class-toggle-applied-text="More"
                                data-sr-css-class-toggle-not-applied-text="Less">
                                More
                            </span>
                            @endif
                        </td>
                        <td>
                            <x-Backend.index-table-info :record="$record" :userList="$userListCache"
                                :options="$index_table_info_options" />
                        </td>
                        <td>
                            <div class="mt-2 mb-2">
                                @if (isset($record->sql_log_file_url))
                                <a href="{{ $record->sql_log_file_url }}" class="btn btn-outline-secondary mb-2">
                                    <i class="fas fa-download"></i>
                                    SQL Log
                                </a>
                                @endif

                                @if (isset($record->sql_dml_log_file_url))
                                <a href="{{ $record->sql_dml_log_file_url }}" class="btn btn-outline-secondary mb-2">
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