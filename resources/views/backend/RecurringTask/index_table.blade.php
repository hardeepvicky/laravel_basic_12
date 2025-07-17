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
                        <th style="width:8%"><?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('department.name', get_value_from_list($field_labels, 'dept_id', 'dept_id')) ?>
                        <th style="width:10%"><?= sortable_anchor('assignToUser.name', get_value_from_list($field_labels, 'assign_to_user_id', 'assign_to_user_id')) ?>
                        <th style="width:5%"><?= sortable_anchor('priority', get_value_from_list($field_labels, 'priority', 'priority')) ?> </th>
                        <th style="width:10%">Time & Duration</th>
                        <th style="width:8%"><?= sortable_anchor('recurring_type', get_value_from_list($field_labels, 'recurring_type', 'recurring_type')) ?></th>
                        <th style="width:5%"><?= sortable_anchor('is_draft', get_value_from_list($field_labels, 'is_draft', 'is_draft')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('is_active', get_value_from_list($field_labels, 'is_active', 'is_active')) ?></th>
                        <th style="width:8%">Task created by system</th>
                        <th style="width:6%"><?= sortable_anchor('created_at', get_value_from_list($field_labels, 'created_at', 'created_at')) ?></th>
                        <th style="width:6%"><?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <?php
                        $first_col_info = [$record->id, $record->assignToUser?->getDisplayName()];
                        ?>
                        <tr>
                            <th scope="row">
                                <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" :labelInfo="$first_col_info"/>
                            </th>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->department?->getDisplayName() }}</td>
                            <td>{{ $record->assignToUser?->getDisplayName() }}</td>
                            <td>{{ $record->priority }}</td>
                            
                            <td>
                                @if ($record->recurring_type != RecurringTask::RECURRING_CUSTOM)
                                    <x-Backend.celec-date-time :value="$record->start_date_time"/>
                                @endif
                                <label class="info">
                                    {{ get_value_from_list($field_labels, 'duration', 'duration') }} :
                                    <span>{{ $record->duration }} {{ get_value_from_list($duration_type_list, $record->duration_type) }}</span>
                                </label>
                            </td>
                            <td>{{ get_value_from_list($recurring_type_list, $record->recurring_type) }}</td>
                            <td>{{ $record->is_draft ? 'Draft' : '' }}</td>
                            <td>
                                <x-Backend.active_deactive :isActive="$record->is_active" :id="$record->id" :routePrefix="$routePrefix" />
                            </td>
                            <td>{{ $record->tasks->count() }}</td>
                            <td>
                                <x-Backend.created-and-by :datetime="$record->created_at" :userId="$record->created_by" />
                            </td>
                            <td>
                                <x-Backend.created-and-by :datetime="$record->updated_at" :userId="$record->updated_by" />
                            </td>
                        </tr>
                        <tr id="record-{{ $record->id }}" class="hidden">
                            <td style="background-color: #FFF;"></td>
                            <td colspan="9">
                                <div class="row">
                                    <div class="col-md-6 col-xl-4">
                                        @if ($record->recurring_type == RecurringTask::RECURRING_CUSTOM)
                                            <div class="card">
                                                <div class="card-header border-l-primary card-no-border">
                                                    <h3>Recurring Custom Dates</h3>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-sm summary-table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Sr. No.</th>
                                                                <th>{{ get_value_from_list($recurring_dates_field_labels, 'date') }}
                                                                </th>
                                                                <th>{{ get_value_from_list($recurring_dates_field_labels, 'task_id') }}
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($record->recurringTaskDate as $i => $recurringTaskDate)
                                                                <tr>
                                                                    <td>{{ $i + 1 }}</td>
                                                                    <td>
                                                                        <x-Backend.celec_date_time label=""
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
                                        @if ($record->recurring_type != RecurringTask::RECURRING_CUSTOM)
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
                                                                        <a target="_blank"
                                                                            href="{{ route('task.index', ['id' => $task->id]) }}">
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
