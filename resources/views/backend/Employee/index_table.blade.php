<?php

use App\Models\User;

?>
<div class="card" id="page-summary">
    <div class="card-header border-t-info">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-bordered summary-table summary_table_with_search">
                <thead>
                    <tr>
                        <th style="width:10%">
                            <div class="row">
                                <div class="col-sm-4">
                                    <span class="btn btn-light btn-sm text-dark border-primary clear_search_conditions mb-1">Clear</span>
                                </div>
                                <div class="col-sm-8">
                                    <?php
                                    $field = 'name';
                                    ?>
                                    <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm" />
                                </div>
                            </div>
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = "type";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" :list="$type_list" :value="$search[$field]"
                                pleaseSelect="All" class="select2 form-select-sm"  />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = "branch_id";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" :list="$branch_list" :value="$search[$field]"
                                pleaseSelect="All" class="select2 form-select-sm"  />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = "department_id";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" :list="$department_list" :value="$search[$field]"
                                pleaseSelect="All" class="select2 form-select-sm"  />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = "designation_id";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" :list="$designation_list" :value="$search[$field]"
                                pleaseSelect="All" class="select2 form-select-sm"  />
                        </th>
                        <th></th>
                        <th style="width:8%">
                            <?php
                            $field = "mobile";
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm"/>
                        </th>                        
                    </tr>
                    <tr class="border-bottom-primary">
                        <th style="width:10%">
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            | <?= sortable_anchor('name', get_value_from_list($field_labels, 'full_name', 'full_name')) ?>
                        </th>
                        <th><?= sortable_anchor('type', get_value_from_list($field_labels, 'type')) ?></th>
                        <th><?= sortable_anchor('branch.name', get_value_from_list($field_labels, 'branch_id', 'branch_id')) ?></th>
                        <th><?= sortable_anchor('department.name', get_value_from_list($field_labels, 'department_id', 'department_id')) ?></th>
                        <th><?= sortable_anchor('designation.name', get_value_from_list($field_labels, 'designation_id', 'designation_id')) ?></th>
                        <th><?= sortable_anchor('email', get_value_from_list($field_labels, 'email')) ?></th>
                        <th><?= sortable_anchor('mobile', get_value_from_list($field_labels, 'mobile')) ?></th>
                        <th style="width:10%"><?= get_value_from_list($field_labels, 'photo') ?></th>
                        <th style="width:8%"><?= sortable_anchor('is_active', get_value_from_list($field_labels, 'is_active')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('created_at', get_value_from_list($field_labels, 'created_at', 'created_at')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                    <tr>
                        <th scope="row">
                            <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" buttons="edit,delete,tr_details">
                                <div class="label">
                                    {{ $record->id . " | " . $record->name }}
                                </div>
                            </x-Backend.celec-summary-table-first-column>
                        </th>
                        <td>{{ get_value_from_list($type_list, $record->type) }}</td>
                        <td>{{ $record->branch?->getDisplayName() }}</td>
                        <td>{{ $record->department?->getDisplayName() }}</td>
                        <td>{{ $record->designation?->getDisplayName() }}</td>
                        <td>{{ $record->email }}</td>
                        <td>{{ $record->mobile }}</td>
                        <td>
                            @if ($url = $record->getProfileImage(null))
                            <a class="fancybox" data-fancybox="group-{{ $record->id }}" href="{{ $url }}">
                                <img class="img-thumbnail rounded-circle small-img" src="{{ $url }}" />
                            </a>
                            @endif
                        </td>
                        <td>
                            <x-Backend.active_deactive :isActive="$record->is_active" :id="$record->id"
                                :routePrefix="$routePrefix" />
                        </td>
                        <td>
                            <x-Backend.created-and-by :datetime="$record->created_at" :userId="$record->created_by" />
                        </td>
                        <td>
                            <x-Backend.created-and-by :datetime="$record->updated_at" :userId="$record->updated_by" />
                        </td>
                    </tr>
                    <tr id="record-{{ $record->id }}" class="hidden">
                        <td style="background-color: var(--body--bs-body-bg)"></td>
                        <td colspan="10">
                            <div class="row">
                                <div class="col-md-6 col-xxl-6">
                                    <div class="card">
                                        <div class="card-header border-l-primary card-no-border">
                                            <h3>Info</h3>
                                        </div>
                                        <div class="card-body">                                            
                                            <label class="info">
                                                {{ get_value_from_list($field_labels, 'whatsapp_mobile') }} :
                                                <span>
                                                    {{ $record->whatsapp_mobile }}
                                                </span>
                                            </label>
                                            <label class="info">
                                                {{ get_value_from_list($field_labels, 'mobile2') }} :
                                                <span>
                                                    {{ $record->mobile2 }}
                                                </span>
                                            </label>
                                            <label class="info">
                                                {{ get_value_from_list($field_labels, 'dob') }} :
                                                <span>
                                                    {{ $record->dob }}
                                                </span>
                                            </label>
                                            <label class="info">
                                                {{ get_value_from_list($field_labels, 'doj') }} :
                                                <span>
                                                    {{ $record->doj }}
                                                </span>
                                            </label>
                                            <label class="info">
                                                {{ get_value_from_list($field_labels, 'current_state_id') }} :
                                                <span>
                                                    {{ $record->currentState->getDisplayName() }}
                                                </span>
                                            </label>
                                            <label class="info">
                                                {{ get_value_from_list($field_labels, 'current_city_id') }} :
                                                <span>
                                                    {{ $record->currentCity->getDisplayName() }}
                                                </span>
                                            </label>
                                            <label class="info">
                                                {{ get_value_from_list($field_labels, 'current_address') }} :
                                                <span>
                                                    {{ $record->current_address }}
                                                </span>
                                            </label>
                                            <label class="info">
                                                {{ get_value_from_list($field_labels, 'current_address2') }} :
                                                <span>
                                                    {{ $record->current_address2 }}
                                                </span>
                                            </label>
                                            <label class="info">
                                                {{ get_value_from_list($field_labels, 'permanent_state_id') }} :
                                                <span>
                                                    {{ $record->permanentState->getDisplayName() }}
                                                </span>
                                            </label>
                                            <label class="info">
                                                {{ get_value_from_list($field_labels, 'permanent_city_id') }} :
                                                <span>
                                                    {{ $record->permanentCity->getDisplayName() }}
                                                </span>
                                            </label>
                                            <label class="info">
                                                {{ get_value_from_list($field_labels, 'permanent_address') }} :
                                                <span>
                                                    {{ $record->permanent_address }}
                                                </span>
                                            </label>
                                            <label class="info">
                                                {{ get_value_from_list($field_labels, 'permanent_address2') }} :
                                                <span>
                                                    {{ $record->permanent_address2 }}
                                                </span>
                                            </label>
                                            <label class="info">
                                                {{ get_value_from_list($field_labels, 'dont_send_sms') }} :
                                                <x-Backend.yes-no-label :value="$record->dont_send_sms" />
                                            </label>
                                            <label class="info">
                                                {{ get_value_from_list($field_labels, 'dont_send_whatsapp_msg') }} :
                                                <x-Backend.yes-no-label :value="$record->dont_send_whatsapp_msg" />
                                            </label>
                                        </div>
                                    </div>
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