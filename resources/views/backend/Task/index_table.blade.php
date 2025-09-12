<?php

use App\Models\Task;
$false = false;
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
            <table class="table table-bordered summary-table summary_table_with_search">
                <thead>
                    <tr>
                        <th style="width:15%">
                            <div class="row">
                                <div class="col-sm-4">
                                    <span class="btn btn-light btn-sm text-dark border-primary clear_search_conditions mb-1">Clear</span>
                                </div>
                                <div class="col-sm-8">
                                    <?php
                                    $field = "assign_to_user_id";
                                    ?>
                                    <x-Inputs.drop-down :name="$field" :label="$false" :value="$search[$field]" :list="$user_list" pleaseSelect="All" class="select2 form-select-sm" />
                                </div>
                            </div>
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = "name";
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = "dept_id";
                            ?>
                            <x-Inputs.drop-down :name="$field" :label="$false" :value="$search[$field]" :list="$department_list" pleaseSelect="All" class="select2 form-select-sm" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = "priority";
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = "status";
                            ?>
                            <x-Inputs.drop-down :name="$field" :label="$false" :value="$search[$field]" :list="$status_list" pleaseSelect="All" class="select2 form-select-sm" />
                        </th>
                    </tr>
                    <tr class="border-bottom-primary">
                        <th>
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            | <?= sortable_anchor('assignToUser.name', get_value_from_list($field_labels, 'assign_to_user_id', 'assign_to_user_id')) ?>
                        </th>
                        <th><?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?></th>
                        <th><?= sortable_anchor('department.name', get_value_from_list($field_labels, 'dept_id', 'dept_id')) ?>
                        <th><?= sortable_anchor('priority', get_value_from_list($field_labels, 'priority', 'priority')) ?> </th>
                        <th><?= sortable_anchor('status', get_value_from_list($field_labels, 'status', 'status')) ?> </th>
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
                            <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" buttons="history,edit,delete,tr_details" :labelInfo="$first_col_info"/>
                        </th>
                        <td>{{ $record->name }}</td>
                        <td>{{ $record->department?->getDisplayName() }}</td>
                        <td>{{ $record->priority }}</td>
                        <td>{{ get_value_from_list($status_list, $record->status) }}</td>
                        <td>
                            <x-Backend.celec-date-time :value="$record->start_date_time"/>
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
                        <td colspan="8">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="info">
                                        {{ get_value_from_list($field_labels, 'description', 'description') }}
                                        :
                                        <span>{!! $record->description !!}</span>
                                    </label>

                                    <label class="info">
                                        {{ get_value_from_list($field_labels, 'tag_name', 'tag_name') }}
                                        :
                                        <span>
                                            {{ implode(", ", $record->tags->pluck("name")->toArray()) }}
                                        </span>
                                    </label>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <h5>
                                                {{ get_value_from_list($field_labels, 'taskUser', 'Link Users') }}
                                            </h5>
                                            <ol>
                                                @foreach ($record->user as $user)
                                                <li>{!! $user->getDisplayName() !!}</li>
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
                                        <div class="col-md-2">
                                            <h5>
                                                {{ get_value_from_list($field_labels, 'taskLead') }}
                                            </h5>
                                            <ol>
                                                @foreach ($record->lead as $lead)
                                                <li>{{ $lead->getDisplayName() }}</li>
                                                @endforeach
                                            </ol>
                                        </div>
                                        <div class="col-md-2">
                                            <h5>
                                                {{ get_value_from_list($field_labels, 'taskCustomer') }}
                                            </h5>
                                            <ol>
                                                @foreach ($record->customer as $customer)
                                                <li>{{ $customer->getDisplayName() }}</li>
                                                @endforeach
                                            </ol>
                                        </div>
                                        <div class="col-md-2">
                                            <h5>
                                                {{ get_value_from_list($field_labels, 'taskDealer') }}
                                            </h5>
                                            <ol>
                                                @foreach ($record->dealer as $dealer)
                                                <li>{{ $dealer->getDisplayName() }}</li>
                                                @endforeach
                                            </ol>
                                        </div>
                                        <div class="col-md-2">
                                            <h5>
                                                {{ get_value_from_list($field_labels, 'taskParty') }}
                                            </h5>
                                            <ol>
                                                @foreach ($record->party as $party)
                                                <li>{{ $party->getDisplayName() }}</li>
                                                @endforeach
                                            </ol>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            @if($record->is_completed)
                                                <x-Backend.celec-date-time :value="$record->complete_date_time"
                                                    label="Completed"
                                                    by="{!! get_value_from_list($userListCache, $record->complete_by) !!}"
                                                />
                                            @endif


                                        </div>

                                        <div class="col-md-2">
                                            @if($record->is_verified)
                                                <x-Backend.celec-date-time :value="$record->verified_date_time"
                                                    label="Verified"
                                                    by="{!! get_value_from_list($userListCache, $record->verified_by) !!}"
                                                />
                                            @endif
                                        </div>

                                        <div class="col-md-3">
                                            @if($record->is_verified)
                                                <label class="info">
                                                    {{ get_value_from_list($field_labels, 'rating', 'rating') }}
                                                    :
                                                    <span>
                                                        <div class="stars">
                                                            <?php for($i = 1; $i <= $record->rating; $i++): ?>
                                                                <label class="star-<?= $i ?> fas fa-star" for="star-<?= $i ?>"></label>
                                                            <?php endfor; ?>
                                                            ({{ $record->rating }})
                                                        </div>
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


                                    <x-Backend.document-files :records="$record->documentFile" />
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