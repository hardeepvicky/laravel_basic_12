<<<<<<< HEAD

<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records"/>
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
                        <th><?= sortable_anchor('name', 'Name') ?></th>
                        <th><?= sortable_anchor('is_admin', 'Admin') ?></th>
                        <th><?= sortable_anchor('is_active', 'Active / De-Active') ?></th>
                        <th><?= sortable_anchor('is_pre_defined', 'Pre-Defined') ?></th>
                        <th>Info</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $record)
                    <tr>
                        <th scope="row">{{ $record->id }}</th>
                        <td>{{ $record->name }}</td>
                        <td>
                            <x-Backend.yes-no-label :value="$record->is_admin"/>
                        </td>
                        <td>
                            <x-Backend.active_deactive :isActive="$record->is_active" :id="$record->id" :routePrefix="$routePrefix"/>
                        </td>
                        <td>
                            <x-Backend.yes-no-label :value="$record->is_pre_defined"/>
                        </td>
                        <td>
                            <x-Backend.index-table-info :record="$record" :userList="$userListCache" />
                        </td>
                        <td>
                            <x-Backend.summary-comman-actions :id="$record->id" :routePrefix="$routePrefix"/>                            
                        </td>
                    </tr>
=======
<div class="card" id="page-summary">
    <div class="card-header border-t-info">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-bordered summary-table summary_table_with_search">
                <thead>
                    <tr>
                        <th>
                            <span class="btn btn-light text-dark border-primary clear_search_conditions mb-1">Clear</span>
                            <?php
                            $field = "name";
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]"/>
                        </th>                         
                    </tr>
                    <tr class="border-bottom-primary">
                        <th style="width:15%">
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            | <?= sortable_anchor('name', get_value_from_list($field_labels, 'full_name', 'full_name')) ?>
                        </th>                        
                        <th style="width:8%"><?= sortable_anchor('is_active', 'Active / De-Active') ?></th>
                        <th style="width:8%"><?= sortable_anchor('is_pre_defined', 'Pre-Defined') ?></th>
                        <th style="width:10%"><?= sortable_anchor('created_at', get_value_from_list($field_labels, 'created_at', 'created_at')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('created_by', get_value_from_list($field_labels, 'created_by', 'created_by')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('updated_by', get_value_from_list($field_labels, 'updated_by', 'updated_by')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <th scope="row">
                                <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" buttons="edit,delete">
                                    <div class="label">
                                        {{ $record->id . " | " . $record->name }}
                                    </div>
                                </x-Backend.celec-summary-table-first-column>
                            </th>
                            <td>
                                <x-Backend.active_deactive :isActive="$record->is_active" :id="$record->id" :routePrefix="$routePrefix" />
                            </td>
                            <td>
                                <x-Backend.yes-no-label :value="$record->is_pre_defined" />
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
>>>>>>> base_v3
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
<<<<<<< HEAD
        <x-Backend.pagination-links :records="$records"/>
    </div>
</div>
=======
        <x-Backend.pagination-links :records="$records" />
    </div>
</div>
>>>>>>> base_v3
