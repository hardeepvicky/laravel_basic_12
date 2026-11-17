<?php
use App\Models\Task;
use App\Models\RecurringTask;
$false = false;
?>
<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-bordered summary-table summary_table_with_search">
                <thead>                    
                    <tr class="border-bottom-primary">
                        <th style="width:5%"><?= sortable_anchor('id', 'ID') ?></th>
                        <th style="width:10%"><?= sortable_anchor('assignToUser.name', 'Assign To') ?></th>
                        <th style="width:10%"><?= sortable_anchor('name', 'Name') ?></th>
                        <th style="width:5%"><?= sortable_anchor('priority', 'Priority') ?> </th>
                        <th style="width:8%"><?= sortable_anchor('recurring_type', 'Recurring Type') ?></th>
                        <th style="width:5%"><?= sortable_anchor('is_draft', 'Draft') ?></th>
                        <th style="width:8%"><?= sortable_anchor('is_active', 'Active') ?></th>
                        <th style="width:6%">Task created by system</th>
                        <th style="width:8%">Time & Duration</th>
                        <th style="width:10%"><?= sortable_anchor('created_at', 'Created') ?></th>
                        <th style="width:10%"><?= sortable_anchor('updated_at', 'Updated') ?></th>
                        <th style="width:10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <?php
                        $first_col_info = [
                            $record->id . " | " . $record->assignToUser?->getDisplayName()
                        ];
                        ?>
                        <tr>
                            <th scope="row">{{ $record->id }}</th>
                            <td>{{$record->assignToUser?->getDisplayName()}} </td>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->priority }}</td>
                            <td>{{ get_value_from_list($recurring_type_list, $record->recurring_type) }}</td>
                            <td>{{ $record->is_draft ? 'Draft' : '' }}</td>
                            <td>
                                <x-Backend.active_deactive :isActive="$record->is_active" :id="$record->id" :routePrefix="$routePrefix" />
                            </td>
                            <td>{{ $record->tasks->count() }}</td>
                            <td>
                                @if ($record->recurring_type != RecurringTask::RECURRING_CUSTOM)
                                    <x-Backend.celec-date-time :value="$record->start_date_time"/>
                                @endif
                                <label class="info">
                                    Duration :
                                    <span>{{ $record->duration }} {{ get_value_from_list($duration_type_list, $record->duration_type) }}</span>
                                </label>
                            </td>   
                            <td>
                                <x-Backend.created_and_by :datetime="$record->created_at" :userId="$record->created_by"/>
                            </td>
                            <td>
                                <x-Backend.created_and_by :datetime="$record->updated_at" :userId="$record->updated_by"/>
                            </td>        
                            <td>
                                <x-Backend.summary-comman-actions :id="$record->id" :routePrefix="$routePrefix" buttons="edit,delete,tr_details" />
                            </td>                         
                        </tr>
                        <tr id="record-{{ $record->id }}" class="hidden">
                            <td style="background-color: var(--body--bs-body-bg)"></td>
                            <td colspan="10">
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
                                                                <th>Date</th>
                                                                <th>Task</th>
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
                                                                        <a target="_blank" href="{{ route('task.index', ['id' => $task->id]) }}">
                                                                            #{{ $task->id }}
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
                                            Description :
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
