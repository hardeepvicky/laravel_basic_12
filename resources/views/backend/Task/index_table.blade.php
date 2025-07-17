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
                        </th>
                        </th>
                        <th><?= sortable_anchor('assignToUser.name', get_value_from_list($field_labels, 'assign_to_user_id', 'assign_to_user_id')) ?>
                        <th><?= sortable_anchor('priority', get_value_from_list($field_labels, 'priority', 'priority')) ?>
                        </th>
                        <th>{{ get_value_from_list($field_labels, 'tag_name', 'tag_name') }}</th>
                        <th>Task Info</th>
                        <th>
                            <?= sortable_anchor('created_at', get_value_from_list($field_labels, 'created_at', 'created_at')) ?>
                            |
                            <?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                    <tr>
                        <th scope="row">
                            <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" />
                        </th>
                        <td>{{ $record->name }}</td>
                        <td>
                            <label class="info">
                                <span>{{ $record->department?->getDisplayName() }}</span>
                                > {{ $record->assignToUser?->getDisplayName() }}
                            </label>
                        </td>
                        <td>{{ $record->priority }}</td>
                        <td>{{ implode(", ", $record->tags->pluck("name")->toArray()) }}</td>
                        <td>
                            <div class="row" id="record-info-{{ $record->id }}">
                                <div class="col-sm-12">
                                    <label class="info">
                                        {{ get_value_from_list($field_labels, 'status', 'status') }} :
                                        <span>{{ get_value_from_list($status_list, $record->status) }}</span>
                                    </label>

                                    <label class="info">
                                        {{ get_value_from_list($field_labels, 'duration', 'duration') }} :
                                        <span>{{ $record->duration }} {{ get_value_from_list($duration_type_list, $record->duration_type) }}</span>
                                    </label>

                                    <x-Backend.celec_date_time label="{{ get_value_from_list($field_labels, 'start_date_time', 'start_date_time') }}" :value="$record->start_date_time"/>
                                    <x-Backend.celec_date_time label="{{ get_value_from_list($field_labels, 'end_date_time', 'end_date_time') }}" :value="$record->end_date_time"/>
                                </div>
                            </div>
                        </td>
                        <td>
                            <x-Backend.index-table-info :record="$record" :userList="$userListCache" />
                        </td>
                    </tr>
                    <tr id="record-{{ $record->id }}" class="hidden">
                        <td style="background-color: #FFF"></td>
                        <td colspan="5">
                            <div class="row">
                                <div class="col-sm-12">
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
                                    <label class="info">
                                        {{ get_value_from_list($field_labels, 'is_send_notification', 'is_send_notification') }} 
                                        : 
                                        <span>
                                            {{ get_value_from_list($yes_no_list, $record->is_send_notification) }}
                                        </span>
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