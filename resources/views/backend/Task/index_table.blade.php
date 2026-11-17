<?php

use App\Models\Task;
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
                        <th><?= sortable_anchor('id', 'ID') ?></th>
                        <th><?= sortable_anchor('assignToUser.name', 'Assign To') ?></th>
                        <th><?= sortable_anchor('name', 'Name') ?></th>                        
                        <th><?= sortable_anchor('priority', 'Priority') ?> </th>
                        <th><?= sortable_anchor('status', 'Status') ?> </th>
                        <th style="width:8%">Time & Duration</th>
                        <th style="width:10%"><?= sortable_anchor('created_at', 'Created At') ?></th>
                        <th style="width:10%"><?= sortable_anchor('updated_at', 'Updated At') ?></th>
                        <th style="width:10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                    <tr>
                        <td>{{ $record->id }}</td>
                        <td>{{ $record->assignToUser?->getDisplayName() }}</td>
                        <td>{{ $record->name }}</td>
                        <td>{{ $record->priority }}</td>
                        <td>{{ get_value_from_list($status_list, $record->status) }}</td>
                        <td>
                            <x-Backend.celec-date-time :value="$record->start_date_time"/>
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
                        <td colspan="8">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="info">                                        
                                        Description : <span>{!! $record->description !!}</span>
                                    </label>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="info">
                                                Send Notification :
                                                <span>
                                                    {{ get_value_from_list($yes_no_list, $record->is_send_notification) }}
                                                </span>
                                            </label>
                                        </div>
                                    </div>

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
                                            Rating :
                                            <span>
                                                {{ $record->rating }}
                                            </span>
                                        </label>

                                        <label class="info">
                                            Review Remarks :
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