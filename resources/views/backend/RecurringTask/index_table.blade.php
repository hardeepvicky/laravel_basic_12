<?php
use App\Models\Task;
use App\Models\RecurringTask;
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
                        <th><?= sortable_anchor('assignToUser.name', get_value_from_list($field_labels, 'assign_to_user_id', 'assign_to_user_id')) ?>
                        <th><?= sortable_anchor('priority', get_value_from_list($field_labels, 'priority', 'priority')) ?> </th>
                        <th><?= sortable_anchor('is_draft', get_value_from_list($field_labels, 'is_draft', 'is_draft')) ?> </th>
                        <th>Task created by system</th>
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
                            <td>
                                <label class="info">
                                    <span>{{ $record->department?->getDisplayName() }}</span>
                                    >
                                    {{ $record->assignToUser?->getDisplayName() }}
                                </label>
                            </td>
                            <td>{{ $record->priority }}</td>
                            <td>{{ $record->is_draft ? "Draft" : "" }}</td>
                            <td>{{ $record->tasks->count() }}</td>
                            <td>
                                <div class="row" id="record-info-{{ $record->id }}">
                                    <div class="col-sm-12">
                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'recurring_type', 'recurring_type') }}
                                            :
                                            <span>{{ get_value_from_list($recurring_type_list, $record->recurring_type) }}</span>
                                        </label>

                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'duration', 'duration') }} :
                                            <span>{{ $record->duration }} {{ get_value_from_list($duration_type_list, $record->duration_type) }}</span>
                                        </label>

                                        @if($record->recurring_type != RecurringTask::RECURRING_CUSTOM)
                                            <x-Backend.celec_date_time
                                                label="{{ get_value_from_list($field_labels, 'start_date_time', 'start_date_time') }}"
                                                :value="$record->start_date_time" />
                                        @endif
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
                                    <div class="col-md-6 col-xl-4">
                                        @if($record->recurring_type == RecurringTask::RECURRING_CUSTOM)
                                        <div class="card">
                                            <div class="card-header border-l-primary card-no-border">
                                                <h3>Recurring Custom Dates</h3>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm summary-table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>{{ get_value_from_list($recurring_dates_field_labels, 'date') }}</th>
                                                            <th>{{ get_value_from_list($recurring_dates_field_labels, 'task_id') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($record->recurringTaskDate as $i => $recurringTaskDate)
                                                            <tr>
                                                                <td>{{ $i + 1 }}</td>
                                                                <td>
                                                                    <x-Backend.celec_date_time
                                                                        label=""
                                                                        :value="$recurringTaskDate->date" />
                                                                </td>
                                                                <td>
                                                                    @if ($recurringTaskDate->task_id)
                                                                        <a target="_blank"
                                                                            href="{{ route('task.index', ['id' => $recurringTaskDate->task_id]) }}">View
                                                                            Task</a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <div class="col-md-6 col-xl-4">
                                        @if($record->recurring_type != RecurringTask::RECURRING_CUSTOM)
                                        <div class="card">
                                            <div class="card-header border-l-primary card-no-border">
                                                <h3>Task created by system</h3>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm summary-table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>Task</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($record->tasks as $i => $task)
                                                            <tr>
                                                                <td>{{ $i + 1 }}</td>
                                                                <td>
                                                                    <a target="_blank" href="{{ route('task.index', ['id' => $task->id]) }}">
                                                                        {{ $task->getDisplayName() }}
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <div class="col-md-6 col-xl-4">
                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'description', 'description') }}
                                            :
                                            <span>{!! $record->description !!}</span>
                                        </label>
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
