<?php

<<<<<<< HEAD
use App\Helpers\FileUtility;
?>

<div class="card" id="page-summary">
    <div class="card-header">
=======
use App\Models\User;

?>
<div class="card" id="page-summary">
    <div class="card-header border-t-info">
>>>>>>> base_v3
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
<<<<<<< HEAD
        <div class="m-2">
            <span class="btn btn-secondary">Export CSV</span>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr class="border-bottom-primary">
                        <th><?= sortable_anchor('id', 'ID') ?></th>
                        <th><?= sortable_anchor('name', 'Name') ?></th>
                        <th><?= sortable_anchor('email', 'Email') ?></th>
                        <th>Profile Photo</th>
                        <th><?= sortable_anchor('is_active', 'Active / De-Active') ?></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $record)
                    <tr>
                        <td>{{ $record->id }}</td>
                        <td>{{ $record->name }}</td>
                        <td>{{ $record->email }}</td>
                        <td>
                            @if($record->profile_image)
                            <a class="fancybox" data-fancybox="group-{{ $record->id }}" href="{{ FileUtility::get($record->profile_image) }}">
                                <img class="img-thumbnail rounded-circle small-img" src="{{ FileUtility::get($record->profile_image) }}" />
=======
        <div class="table-responsive">
            <table class="table table-bordered summary-table summary_table_with_search">
                <thead>
                    <tr>
                        <th>
                            <?php
                            $field = "name";
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]"/>
                        </th>                        
                    </tr>
                    <tr class="border-bottom-primary">
                        <th style="width:10%">
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            | <?= sortable_anchor('name', get_value_from_list($field_labels, 'full_name', 'full_name')) ?>
                        </th>
                        <th><?= sortable_anchor('type', get_value_from_list($field_labels, 'type')) ?></th>
                        <th><?= sortable_anchor('sub_type', get_value_from_list($field_labels, 'sub_type')) ?></th>
                        <th><?= get_value_from_list($field_labels, 'user_category_id') ?></th>
                        <th><?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'Username')) ?></th>
                        <th><?= sortable_anchor('email', get_value_from_list($field_labels, 'email')) ?></th>
                        <th><?= sortable_anchor('mobile', get_value_from_list($field_labels, 'mobile')) ?></th>
                        <th style="width:10%"><?= get_value_from_list($field_labels, 'profile_image') ?></th>
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
                                    {{ $record->id . " | " . $record->full_name }}
                                </div>
                            </x-Backend.celec-summary-table-first-column>
                        </th>
                        <td>{{ get_value_from_list($type_list, $record->type) }}</td>
                        <td>{{ get_value_from_list($sub_type_list, $record->sub_type) }}</td>
                        <td>{{ $record->userCategory?->getDisplayName() }}</td>
                        <td>{{ $record->name }}</td>
                        <td>{{ $record->email }}</td>
                        <td>{{ $record->mobile }}</td>
                        <td>
                            @if ($url = $record->getProfileImage(null))
                            <a class="fancybox" data-fancybox="group-{{ $record->id }}" href="{{ $url }}">
                                <img class="img-thumbnail rounded-circle small-img" src="{{ $url }}" />
>>>>>>> base_v3
                            </a>
                            @endif
                        </td>
                        <td>
<<<<<<< HEAD
                            <x-Backend.active_deactive :isActive="$record->is_active" :id="$record->id" :routePrefix="$routePrefix" />
                        </td>
                        <td>
                            <x-Backend.summary-comman-actions :id="$record->id" :routePrefix="$routePrefix" />

                            <span class="btn btn-info btn-sm css-toggler mt-1 mb-1"
                                data-sr-css-class-toggle-target="#record-{{ $record->id }}"
                                data-sr-css-class-toggle-class="hidden">
                                Details
                            </span>
                        </td>
                    </tr>
                    <tr id="record-{{ $record->id }}" class="hidden">
                        <td></td>
                        <td colspan="4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header border-l-primary card-no-border">
                                            <h3>Info</h3>
                                        </div>  
                                        <div class="card-body">
                                            <label class="info">
                                                Don't Send Email :
                                                <x-Backend.yes-no-label :value="$record->dont_send_email" />
                                            </label>
                                            <label class="info">
                                                Pre Defined :
=======
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
                                <div class="col-md-6 col-xxl-4">
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
                                                {{ get_value_from_list($field_labels, 'voip_mobile') }} :
                                                <span>
                                                    {{ $record->voip_mobile }}
                                                </span>
                                            </label>
                                            <label class="info">
                                                {{ get_value_from_list($field_labels, 'dinstar_port') }} :
                                                <span>
                                                    {{ $record->dinstar_port }}
                                                </span>
                                            </label>
                                            <label class="info">
                                                {{ get_value_from_list($field_labels, 'office_landline_no') }} :
                                                <span>
                                                    {{ $record->office_landline_no }}
                                                </span>
                                            </label>
                                            <label class="info">
                                                {{ get_value_from_list($field_labels, 'dont_send_email') }} :
                                                <x-Backend.yes-no-label :value="$record->dont_send_email" />
                                            </label>
                                            <label class="info">
                                                {{ get_value_from_list($field_labels, 'is_pre_defined') }} :
>>>>>>> base_v3
                                                <x-Backend.yes-no-label :value="$record->is_pre_defined" />
                                            </label>

                                            <hr>
                                            <h5>Roles</h5>
                                            <ol>
<<<<<<< HEAD
                                            @foreach($record->getRolesList() as $role_id => $role_name)
                                                <li>{{ $role_name}}</li>
                                            @endforeach
                                            </ol>

                                            
                                            @if(is_array($record->used_in_other_table_created_by))
                                            <hr>
                                            <h5>This User created records Of Other Modules</h5>
                                            <ol>
                                                @foreach($record->used_in_other_table_created_by as $arr)                                        
                                                <li>
                                                    <strong>{{ $arr['label'] }} : </strong>
                                                    <span>{{ $arr['counter'] }}</span>
                                                </li>
                                                @endforeach
                                            </ol>
                                            @endif

                                            
                                            @if(is_array($record->used_in_other_table_updated_by))
                                            <hr>
                                            <h5>This User updated records Of Other Modules</h5>
                                            <ol>
                                                @foreach($record->used_in_other_table_updated_by as $arr)                                        
                                                <li>
                                                    <strong>{{ $arr['label'] }} : </strong>
                                                    <span>{{ $arr['counter'] }}</span>
                                                </li>
                                                @endforeach
                                            </ol>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <x-Backend.index-table-info :record="$record" :userList="$userListCache" />
                                </div>
                            </div>
=======
                                                @foreach ($record->getRolesList() as $role_id => $role_name)
                                                <li>{{ $role_name }}</li>
                                                @endforeach
                                            </ol>

                                            <label class="info">
                                                {{ get_value_from_list($field_labels, 'created_via') }} :
                                                <span>
                                                    {{ get_value_from_list(User::CREATED_VIA_LIST, $record->created_via) }}
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xxl-4">
                                    <div class="card">
                                        <div class="card-header border-l-secondary card-no-border">
                                            <h3>Allowed IP</h3>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm summary-table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Sr. No.</th>
                                                        <th>{{ get_value_from_list($user_allow_ip_field_labels, 'ip') }}
                                                        </th>
                                                        <th>{{ get_value_from_list($user_allow_ip_field_labels, 'description') }}
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($record->userAllowIp as $i => $user_allow_ip)
                                                    <tr>
                                                        <td>{{ $i + 1 }}</td>
                                                        <td>{{ $user_allow_ip->ip }}</td>
                                                        <td>{{ $user_allow_ip->description }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>                                    
                                </div>
                            </div>                            
>>>>>>> base_v3
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