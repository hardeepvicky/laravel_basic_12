<?php

use App\Models\Task;
?>
<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="m-2">
            <a href="{{ route($routePrefix . ".export.csv") }}" class="btn btn-secondary">Export CSV</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered summary-table">
                <thead>
                    <tr class="border-bottom-primary">
                        <th style="width:5%"><?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?></th>                        
                        <th style="width:10%"><?= sortable_anchor('department.name', get_value_from_list($field_labels, 'dept_id', 'dept_id')) ?>
                        <th style="width:10%"><?= sortable_anchor('assignToUser.name', get_value_from_list($field_labels, 'assign_to_user_id', 'assign_to_user_id')) ?>
                        <th style="width:5%"><?= sortable_anchor('priority', get_value_from_list($field_labels, 'priority', 'priority')) ?> </th>
                        <th style="width:8%">Time & Duration</th>
                        <th style="width:5%"><?= sortable_anchor('status', get_value_from_list($field_labels, 'status', 'status')) ?> </th>
                        <th style="width:10%">{{ get_value_from_list($field_labels, 'tag_name', 'tag_name') }}</th>
                        <th style="width:6%"><?= sortable_anchor('created_at', get_value_from_list($field_labels, 'created_at', 'created_at')) ?></th>
                        <th style="width:6%"><?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                    <?php
                        $first_col_info = [
                            $record->id,
                            $record->assignToUser?->getDisplayName()
                        ];
                    ?>
                    <tr>
                        <th scope="row">
                            <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" buttons="history,edit,delete" :labelInfo="$first_col_info"/>
                        </th>
                        <td>{{ $record->name }}</td>
                        <td>{{ $record->department?->getDisplayName() }}</td>
                        <td>{{ $record->assignToUser?->getDisplayName() }}</td>
                        <td>{{ $record->priority }}</td>
                        <td>
                            <x-Backend.celec-date-time :value="$record->start_date_time"/>
                            <label class="info">
                                {{ get_value_from_list($field_labels, 'duration', 'duration') }} :
                                <span>{{ $record->duration }} {{ get_value_from_list($duration_type_list, $record->duration_type) }}</span>
                            </label>
                        </td>
                        <td>{{ get_value_from_list($status_list, $record->status) }}</td>
                        <td>{{ implode(", ", $record->tags->pluck("name")->toArray()) }}</td>
                        <td>
                            <x-Backend.created-and-by :datetime="$record->created_at_user_tz" :userId="$record->created_by" />
                        </td>
                        <td>
                            <x-Backend.created-and-by :datetime="$record->updated_at_user_tz" :userId="$record->updated_by" />
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

                                    @if($record->is_completed)
                                        <x-Backend.celec-date-time :value="$record->complete_date_time" 
                                            label="Completed"
                                            by="{{ get_value_from_list($userListCache, $record->complete_by) }}"
                                        />                                    
                                    @endif

                                    @if($record->is_verified)
                                        <x-Backend.celec-date-time :value="$record->verified_date_time" 
                                            label="Verified"
                                            by="{{ get_value_from_list($userListCache, $record->verified_by) }}"
                                        />

                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'rating', 'rating') }} 
                                            : 
                                            <span>
                                                {{ $record->rating }}
                                            </span>
                                        </label>

                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'review_remarks', 'review_remarks') }} 
                                            : 
                                            <span>
                                                {{ $record->review_remarks }}
                                            </span>
                                        </label>
                                    @endif

                                    
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