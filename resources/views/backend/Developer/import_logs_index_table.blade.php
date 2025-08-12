<?php
use App\Helpers\Util;
?>
<div class="card" id="page-summary">
    <div class="card-header border-t-info">
        <x-backend.pagination-links :records="$records" />
    </div>

    <div class="card-body">
        <table class="table table-striped table-bordered table-hover mb-0">
            <thead>
                <tr>
                    <th><?= sortable_anchor('type', 'Type') ?></th>
                    <th>File</th>
                    <th><?= sortable_anchor('total_count', 'Total Count') ?></th>
                    <th><?= sortable_anchor('accept_count', 'Accept Count') ?></th>
                    <th style="width:8%"><?= sortable_anchor('is_fully_run', 'Fully Run') ?></th>
                    <th style="width:8%"><?= sortable_anchor('exec_time', 'Execution Time') ?></th>
                    <th style="width:25%"><?= sortable_anchor('have_error', 'Have Error') ?></th>
                    <th style="width:12%"><?= sortable_anchor('created_at', 'Created') ?></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $record)
                    <tr>
                        <td>{{ $record->type }}</td>
                        <td>
                            <span class="btn btn-outline-light text-dark summary-action-button mb-2 css-toggler"
                                data-sr-css-class-toggle-target="#record-{{ $record->id }}"
                                data-sr-css-class-toggle-class="hidden">
                                Details
                            </span>

                            @if($record->file_url)
                                <a href="{{ $record->file_url }}" class="btn btn-outline-secondary mb-2">
                                    <i class="fas fa-download"></i> Download File
                                </a>
                            @endif
                        </td>
                        <td>{{ $record->total_count }}</td>
                        <td>{{ $record->accept_count }}</td>
                        <td>{{ $record->is_fully_run ? "Fully Run" : "-" }}</td>
                        <td>{{ $record->exec_time ? Util::niceTime($record->exec_time) : "-" }}</td>
                        <td>{!! $record->have_error ? '<span class="bg-light-danger p-1">' . $record->flash_msg . '</span>' : '<span class="bg-light-success p-1">' . $record->flash_msg . '</span>' !!}</td>
                        <td>
                            <x-Backend.created-and-by :datetime="$record->created_at_user_tz" :userId="$record->created_by" />
                        </td>
                    </tr>
                    <tr id="record-{{ $record->id }}" class="hidden">
                        <td style="background-color: var(--body--bs-body-bg)"></td>
                        <td colspan="3">
                            @if(isset($record->errors) && is_array($record->errors))                            
                                <x-Backend.form-errors :customErrors="$record->errors" />
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <x-backend.pagination-links :records="$records" />
    </div>
</div>
