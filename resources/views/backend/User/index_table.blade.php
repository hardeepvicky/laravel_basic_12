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
                    <tr class="border-bottom-primary">
                        <th style="width:5%"><?= sortable_anchor('id', 'ID') ?></th>
                        <th><?= sortable_anchor('name', 'Name') ?></th>
                        <th><?= sortable_anchor('type', 'Type') ?></th>
                        <th><?= sortable_anchor('email', 'Email') ?></th>
                        <th><?= sortable_anchor('mobile', 'Mobile') ?></th>
                        <th style="width:10%">Profile Image</th>
                        <th style="width:8%"><?= sortable_anchor('is_active', 'Active') ?></th>
                        <th style="width:8%"><?= sortable_anchor('created_at', 'Created') ?></th>
                        <th style="width:8%"><?= sortable_anchor('updated_at', 'Updated') ?></th>
                        <th style="width:10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                    <tr>
                        <td>{{ $record->id }}</td>
                        <td>{{ get_value_from_list($type_list, $record->type) }}</td>
                        <td>{{ $record->name }}</td>
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
                        <td>
                            <x-Backend.summary-comman-actions :id="$record->id" :routePrefix="$routePrefix" buttons="edit,delete, tr_details" />
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
                                                Dont Send Email :
                                                <x-Backend.yes-no-label :value="$record->dont_send_email" />
                                            </label>
                                            <label class="info">
                                                Pre Defined :
                                                <x-Backend.yes-no-label :value="$record->is_pre_defined" />
                                            </label>

                                            <hr>
                                            <h5>Roles</h5>
                                            <ol>
                                                @foreach ($record->getRolesList() as $role_id => $role_name)
                                                <li>{{ $role_name }}</li>
                                                @endforeach
                                            </ol>
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