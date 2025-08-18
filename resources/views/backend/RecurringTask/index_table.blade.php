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
                    <tr>
                        <th style="width:10%">
                            <span class="btn btn-light text-dark border-primary clear_search_conditions mb-1">Clear</span>
                            <?php
                            $field = "assign_to_user_id";
                            ?>
                            <x-Inputs.drop-down :name="$field" :label="$false" :value="$search[$field]" :list="$user_list" class="select2"/>
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = "name";
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = "dept_id";
                            ?>
                            <x-Inputs.drop-down :name="$field" :label="$false" :value="$search[$field]" :list="$department_list"  class="select2"/>
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = "priority";
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = "recurring_type";
                            ?>
                            <x-Inputs.drop-down :name="$field" :label="$false" :value="$search[$field]" :list="$recurring_type_list"  class="select2"/>
                        </th>
                    </tr>
                    <tr class="border-bottom-primary">
                        <th style="width:10%">
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            | <?= sortable_anchor('assignToUser.name', get_value_from_list($field_labels, 'assign_to_user_id', 'assign_to_user_id')) ?>
                        </th>
                        <th style="width:10%"><?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?></th>
                        <th style="width:5%"><?= sortable_anchor('department.name', get_value_from_list($field_labels, 'dept_id', 'dept_id')) ?>
                        <th style="width:5%"><?= sortable_anchor('priority', get_value_from_list($field_labels, 'priority', 'priority')) ?> </th>
                        <th style="width:8%"><?= sortable_anchor('recurring_type', get_value_from_list($field_labels, 'recurring_type', 'recurring_type')) ?></th>
                        <th style="width:5%"><?= sortable_anchor('is_draft', get_value_from_list($field_labels, 'is_draft', 'is_draft')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('is_active', get_value_from_list($field_labels, 'is_active', 'is_active')) ?></th>
                        <th style="width:6%">Task created by system</th>
                        <th style="width:8%">Time & Duration</th>
                        <th style="width:10%"><?= sortable_anchor('created_at', get_value_from_list($field_labels, 'created_at', 'created_at')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('created_by', get_value_from_list($field_labels, 'created_by', 'created_by')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('updated_by', get_value_from_list($field_labels, 'updated_by', 'updated_by')) ?></th>
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
                            <th scope="row">
                                <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" :labelInfo="$first_col_info" buttons="edit,delete,tr_details" />
                            </th>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->department?->getDisplayName() }}</td>
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
                                    {{ get_value_from_list($field_labels, 'duration', 'duration') }} :
                                    <span>{{ $record->duration }} {{ get_value_from_list($duration_type_list, $record->duration_type) }}</span>
                                </label>
                            </td>
                            <td>
                                <x-Backend.celec-date-time :value="$record->created_at" />
                            </td>
                            <td>
                                {{ get_value_from_list($userListCache, $record->created_by) }}
                            </td>
                            <td>
                                <x-Backend.celec-date-time :value="$record->updated_at" :userId="$record->updated_by" />
                            </td>
                            <td>
                                {{ get_value_from_list($userListCache, $record->updated_by) }}
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
                                            {{ get_value_from_list($field_labels, 'description', 'description') }}
                                            :
                                            <span>{!! $record->description !!}</span>
                                        </label>

                                        <h5>
                                            Link Users
                                        </h5>
                                        <ol>
                                            @foreach ($record->user as $user)
                                            <li>{{ $user->getDisplayName() }}</li>
                                            @endforeach
                                        </ol>
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
