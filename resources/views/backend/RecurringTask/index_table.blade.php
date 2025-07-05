<?php
use App\Models\Task;
?>
<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-bordered summary-table">
                <thead>
                    <tr class="border-bottom-primary">
                        <th><?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?></th>
                        <th><?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?></th>
                        <th><?= sortable_anchor('task_type', get_value_from_list($field_labels, 'task_type', 'task_type')) ?></th>
                        <th><?= sortable_anchor('description', get_value_from_list($field_labels, 'description', 'description')) ?>
                        </th>                        
                        <th>Assign To</th>
                        <th><?= sortable_anchor('priority', get_value_from_list($field_labels, 'priority', 'priority')) ?>
                        </th>
                        <th>Task Info</th>
                        <th>
                            <?= sortable_anchor('created_at', get_value_from_list($field_labels, 'created_at', 'created_at')) ?>
                            |
                            <?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?>
                        </th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <th scope="row">{{ $record->id }}</th>
                            <td>{{ $record->name }}</td>
                            <td>{{ get_value_from_list($type_list, $record->task_type) }}</td>
                            <td>{{ $record->description }}</td>
                            
                            <td>{{ $record->assignToUser?->getDisplayName() }}</td>
                            <td>{{ $record->priority }}</td>
                            <td>
                                <div class="row" id="record-info-{{ $record->id }}">
                                    <div class="col-sm-12">
                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'recurring_type', 'recurring_type') }}
                                            :
                                            <span>{{ get_value_from_list($recurring_type_list, $record->recurring_type) }}</span>
                                        </label>
                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'complete_time_in_days', 'complete_time_in_days') }} :
                                            <span>{{ get_value_from_list($complete_in_days_list, $record->complete_time_in_days) }}</span>
                                        </label>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <x-Backend.index-table-info :record="$record" :userList="$userListCache" />
                            </td>
                            <td>
                                <x-Backend.summary-comman-actions :id="$record->id" :routePrefix="$routePrefix" />
                                <span class="btn btn-info btn-sm css-toggler mt-1 mb-1"
                                    data-sr-css-class-toggle-target="#record-{{ $record->id }}"
                                    data-sr-css-class-toggle-class="hidden" data-sr-css-class-toggle-applied-text="Show"
                                    data-sr-css-class-toggle-not-applied-text="Hide">
                                    Show
                                </span>
                            </td>
                        </tr>
                        <tr id="record-{{ $record->id }}" class="hidden">
                            <td></td>
                            <td colspan="7">
                                <div class="row">
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card">
                                            <div class="card-header border-l-primary card-no-border">
                                                <h3>Recurring Custom Dates</h3>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm summary-table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($record->taskRecurringDate as $i => $taskRecurringDate)
                                                            <tr>
                                                                <td>{{ $i + 1 }}</td>
                                                                <td>{{ if_date($taskRecurringDate->date) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
