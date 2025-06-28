<?php
use App\Models\User;
?>
<div class="card" id="page-summary">
    <div class="card-header border-t-info">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="m-2">
            <span class="btn btn-secondary">Export CSV</span>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr class="border-bottom-primary">
                        <th><?= sortable_anchor('id', 'ID') ?></th>
                        <th><?= sortable_anchor('type', 'Type') ?></th>
                        <th>Category</th>
                        <th><?= sortable_anchor('name', 'Name') ?></th>
                        <th><?= sortable_anchor('email', 'Email') ?></th>
                        <th>Mobile</th>
                        <th>Profile Photo</th>
                        <th><?= sortable_anchor('is_active', 'Active / De-Active') ?></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <td>{{ $record->id }}</td>
                            <td>{{ get_value_from_list($type_list, $record->type) }}</td>
                            <td>{{ $record->userCategory?->getDisplayName() }}</td>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->email }}</td>
                            <td>{{ $record->mobile }}</td>
                            <td>
                                @if ($url = $record->getProfileImage())
                                    <a class="fancybox" data-fancybox="group-{{ $record->id }}"
                                        href="{{ $url }}">
                                        <img class="img-thumbnail rounded-circle small-img" src="{{ $url }}" />
                                    </a>
                                @endif
                            </td>
                            <td>
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
                            <td colspan="7">
                                <div class="row">
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card">
                                            <div class="card-header border-l-primary card-no-border">
                                                <h3>Info</h3>
                                            </div>
                                            <div class="card-body">
                                                <label class="info">
                                                    Person Name :
                                                    <span>
                                                        {{ $record->person_name }}
                                                    </span>
                                                </label>
                                                <label class="info">
                                                    Don't Send Email :
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

                                                <label class="info">
                                                    Created Via :
                                                    <span>
                                                        {{ get_value_from_list(User::CREATED_VIA_LIST, $record->created_via) }}
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-8">
                                        <x-Backend.index-table-info :record="$record" :userList="$userListCache" />

                                        @if (is_developer_role_login())
                                            @if (is_array($record->used_in_other_table_created_by))
                                                <hr>
                                                <h5>This User create records Of Other Modules</h5>
                                                <ol>
                                                    @foreach ($record->used_in_other_table_created_by as $arr)
                                                        <li>
                                                            <strong>{{ $arr['label'] }} : </strong>
                                                            <span>{{ $arr['counter'] }}</span>
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            @endif


                                            @if (is_array($record->used_in_other_table_updated_by))
                                                <hr>
                                                <h5>This User update records Of Other Modules</h5>
                                                <ol>
                                                    @foreach ($record->used_in_other_table_updated_by as $arr)
                                                        <li>
                                                            <strong>{{ $arr['label'] }} : </strong>
                                                            <span>{{ $arr['counter'] }}</span>
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            @endif
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
