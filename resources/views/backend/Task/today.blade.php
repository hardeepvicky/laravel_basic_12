@extends($layout)

@section('content')
    <?php
    use App\Models\Task;
    
    $page_header_links[] = ['title' => 'List', 'url' => route($routePrefix . '.index')];
    $page_header_links[] = ['title' => 'Add', 'url' => route($routePrefix . '.create')];
    
    ?>

    @include($partial_path . '.page_header')

    <div class="row">
        <div class="col-md-12 col-lg-5">
            <div class="card">
                <div class="card-header border-l-primary card-no-border">
                    <h3>My Tasks</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered summary-table table-sm">
                            <thead>
                                <tr class="border-bottom-primary">
                                    <th>Sr. No.</th>
                                    <th><?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?>
                                    </th>
                                    <th><?= sortable_anchor('priority', get_value_from_list($field_labels, 'priority', 'priority')) ?>
                                    <th><?= sortable_anchor('start_date_time', get_value_from_list($field_labels, 'start_date_time', 'start_date_time')) ?>
                                    <th><?= sortable_anchor('end_date_time', get_value_from_list($field_labels, 'end_date_time', 'end_date_time')) ?>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($my_tasks as $i => $record)
                                    <tr>
                                        <th scope="row">{{ $i + 1 }}</th>
                                        <td>{{ $record->name }}</td>
                                        <td>{{ $record->priority }}</td>
                                        <td>
                                            {{ if_date($record->start_date_time) }}
                                        </td>
                                        <td>
                                            {{ if_date($record->end_date_time) }}
                                        </td>
                                        <td>
                                            {{ get_value_from_list($status_list, $record->status) }}
                                        </td>
                                        <td>
                                            @if ($record->status == Task::STATUS_ASSIGNED)
                                                <a class="btn btn-secondary btn-sm mb-2 confirm"
                                                    href="{{ route($routePrefix . '.status_confirm', ['id' => $record->id]) }}">
                                                    Confirm Now
                                                </a>

                                                <a class="btn btn-secondary btn-sm mb-2 confirm"
                                                    href="{{ route($routePrefix . '.status_in_progress', ['id' => $record->id]) }}">
                                                    {{ get_value_from_list($status_list, Task::STATUS_IN_PROGRESS) }}
                                                    Move To In-Progress
                                                </a>
                                            @endif

                                            @if (in_array($record->status, [Task::STATUS_CONFIRM]))
                                                <a class="btn btn-secondary btn-sm mb-2 confirm"
                                                    href="{{ route($routePrefix . '.status_in_progress', ['id' => $record->id]) }}">
                                                    Move To In-Progress
                                                </a>
                                            @endif

                                            @if (in_array($record->status, [Task::STATUS_IN_PROGRESS]))
                                                <a class="btn btn-secondary btn-sm mb-2 confirm"
                                                    href="{{ route($routePrefix . '.status_confirm', ['id' => $record->id]) }}">
                                                    Revert To Previous Status
                                                </a>

                                                <a class="btn btn-secondary btn-sm mb-2 confirm"
                                                    href="{{ route($routePrefix . '.status_complete', ['id' => $record->id]) }}">
                                                    Complete Now
                                                </a>
                                            @endif

                                            @if (in_array($record->status, [Task::STATUS_COMPLETED]))
                                                <a class="btn btn-secondary btn-sm mb-2 confirm"
                                                    href="{{ route($routePrefix . '.status_verified', ['id' => $record->id]) }}">
                                                    Verify Now
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-7">
            <div class="card">
                <div class="card-header border-1-secondary card-no-border">
                    <h3>Assign To Others</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route($routePrefix . '.today') }}">
                        <input type="hidden" name="is_sort_clear" value="1" />
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <?php
                                $field = 'status';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down name="other_task_status" :label="$label" :value="$search['other_task_status']"
                                    :list="$status_list" class="select2" />
                            </div>
                            <div class="col-md-6">
                                <?php
                                $field = 'assign_to_user_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down name="other_assign_to_user_id" :label="$label" :value="$search['other_assign_to_user_id']"
                                    :list="$user_list" class="select2" />
                            </div>
                        </div>
                        <?php $false = false; ?>
                        <x-Backend.summary-search-form-footer :showPaginationDropDown="$false" />
                    </form>

                    <div class="table-responsive mt-2">
                        <table class="table table-bordered summary-table table-sm">
                            <thead>
                                <tr class="border-bottom-secondary">
                                    <th>Sr. No.</th>
                                    <th><?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?>
                                    </th>
                                    <th>Assign To</th>
                                    <th><?= sortable_anchor('priority', get_value_from_list($field_labels, 'priority', 'priority')) ?>
                                    <th><?= sortable_anchor('start_date_time', get_value_from_list($field_labels, 'start_date_time', 'start_date_time')) ?>
                                    <th><?= sortable_anchor('end_date_time', get_value_from_list($field_labels, 'end_date_time', 'end_date_time')) ?>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $i => $record)
                                    <tr>
                                        <th scope="row">{{ $i + 1 }}</th>
                                        <td>{{ $record->name }}</td>
                                        <td>{{ $record->assignToUser?->getDisplayName() }}</td>
                                        <td>{{ $record->priority }}</td>
                                        <td>
                                            {{ if_date($record->start_date_time) }}
                                        </td>
                                        <td>
                                            {{ if_date($record->end_date_time) }}
                                        </td>
                                        <td>
                                            {{ get_value_from_list($status_list, $record->status) }}
                                        </td>
                                        <td>
                                            @if (in_array($record->status, [Task::STATUS_COMPLETED]) && in_array($record->created_by, [0, $auth_user->id]))
                                                <a class="btn btn-secondary btn-sm mb-2 confirm"
                                                    href="{{ route($routePrefix . '.status_verified', ['id' => $record->id]) }}">
                                                    Verify Now
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
