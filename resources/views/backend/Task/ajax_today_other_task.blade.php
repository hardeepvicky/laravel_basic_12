<?php
use App\Models\Task;
$page = (int) request("page", 1);
$start = ($page * 10) - 10;
?>
<div class="card">
    <div class="card-header">
        <x-Backend.pagination-links :records="$other_tasks" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive mt-2">
            <table class="table table-bordered summary-table table-sm">
                <thead>
                    <tr class="border-bottom-secondary">
                        <th>Sr. No.</th>
                        <th><?= sortable_anchor('name', get_value_from_list($field_labels, 'name')) ?>
                        </th>
                        <th>Assign To</th>
                        <th><?= sortable_anchor('priority', get_value_from_list($field_labels, 'priority')) ?>
                        <th><?= sortable_anchor('start_date_time', get_value_from_list($field_labels, 'start_date_time')) ?>
                        <th>Duration</th>
                        <th><?= sortable_anchor('end_date_time', get_value_from_list($field_labels, 'end_date_time')) ?>
                        <th>Time Left</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = $start;
                    @endphp
                    @foreach ($other_tasks as $record)
                    @php
                        $i++;
                    @endphp
                        <tr data-task_id="{{ $record->id }}">
                            <th scope="row">{{ $i }}</th>
                            <td class="name">{{ $record->name }}</td>
                            <td class="assignToUser">{{ $record->assignToUser?->getDisplayName() }}</td>
                            <td>{{ $record->priority }}</td>
                            <td>
                                <x-Backend.celec-date-time :value="$record->start_date_time"/>
                            </td>
                            <td>
                                {{ $record->duration }}
                                {{ get_value_from_list($duration_type_list, $record->duration_type) }}
                            </td>
                            <td>
                                <x-Backend.celec-date-time :value="$record->end_date_time"/>
                            </td>
                            <td>{{ $record->time_left }}</td>
                            <td>
                                {{ get_value_from_list($status_list, $record->status) }}
                            </td>
                            <td>
                                @if (in_array($record->status, [Task::STATUS_COMPLETED]))
                                    <a class="btn btn-secondary btn-sm mb-2 status_verified"
                                        href="{{ route($routePrefix . '.status_verified', ['id' => $record->id]) }}">
                                        Verify Now
                                    </a>
                                @endif

                                <a class="btn btn-secondary btn-sm mb-2"
                                    href="{{ route($routePrefix . '.index', ['id' => $record->id]) }}">
                                    Go To List
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer">
        <x-Backend.pagination-links :records="$other_tasks" />
    </div>
</div>