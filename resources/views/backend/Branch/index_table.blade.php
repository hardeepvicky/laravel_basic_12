<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="m-2">
            <a href="{{ route($routePrefix . '.export.csv') }}" class="btn btn-secondary">Export CSV</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered summary-table summary_table_with_search">
                <thead>
                    <tr>
                        <th>
                            <span class="btn btn-light text-dark border-primary clear_search_conditions mb-1">Clear</span>
                            <?php
                            $field = "name";
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$search[$field]"/>
                        </th>
                        <th>
                            <?php
                            $field = "type";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" :list="$type_list" :value="$search[$field]" class="select2" />
                        </th>
                        <th>
                            <?php
                            $field = "state_id";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" :list="$state_list" :value="$search[$field]" class="select2 mb-1"/>

                            <?php
                            $field = "city_id";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" :list="$district_city_list" :value="$search[$field]" class="select2" />
                        </th>
                        <th>
                            <?php
                            $field = "mobile";
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$search[$field]"/>
                        </th>
                        <th>
                            <?php
                            $field = "phone_no";
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$search[$field]"/>
                        </th>
                    </tr>
                    <tr class="border-bottom-primary">
                        <th style="width:12%">
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            | <?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?>
                        </th>
                        <th style="width:10%">
                            <?= sortable_anchor('type', get_value_from_list($field_labels, 'type', 'type')) ?>
                        </th>
                        <th style="width:12%">
                            <?= sortable_anchor('state.name', get_value_from_list($field_labels, 'state_id', 'state_id')) ?>
                            | <?= sortable_anchor('distirct.name', get_value_from_list($field_labels, 'district_id', 'district_id')) ?>
                            | <?= sortable_anchor('city.name', get_value_from_list($field_labels, 'city_id', 'city_id')) ?>
                        </th>
                        <th style="width:8%"><?= sortable_anchor('mobile', get_value_from_list($field_labels, 'mobile', 'mobile')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('phone_no', get_value_from_list($field_labels, 'phone_no', 'phone_no')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('list_display_order', get_value_from_list($field_labels, 'list_display_order', 'list_display_order')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('is_active', 'Activate / De-Active') ?></th>
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
                                <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" buttons="edit,copy,delete,tr_details">
                                    <div class="label">
                                        {{ $record->id . " | " . $record->name }}
                                    </div>
                                </x-Backend.celec-summary-table-first-column>
                            </th>
                            <td>{{ get_value_from_list($type_list, $record->type) }}</td>
                            <td>
                                {{ $record->state?->getShortName() }}
                                >> {{ $record->district?->getDisplayName() }}
                                >> {{ $record->city?->getDisplayName() }}
                            </td>
                            <td>{{ $record->mobile }}</td>
                            <td>{{ $record->phone_no }}</td>
                            <td class="text-center">
                                <a class="x-editable" href="javascript:void();"id="list_display_order" data-type="number"
                                    data-name="list_display_order"
                                    data-pk="{{ $record->id }}"
                                    data-url="{{ route($routePrefix . '.ajax_update_field') }}"
                                    data-title="Enter Display Order">
                                    {{ $record->list_display_order }}
                                </a>
                            </td>
                            <td>
                                <x-Backend.active_deactive :isActive="$record->is_active" :id="$record->id" :routePrefix="$routePrefix" />
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
                            <td colspan="9">
                                <div class="row">
                                    <div class="col-md-6 col-xl-4">
                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'email') }} :
                                            <span>
                                                {{ $record->email }}
                                            </span>
                                        </label>

                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'gst_no') }} :
                                            <span>
                                                {{ $record->gst_no }}
                                            </span>
                                        </label>

                                        <h4 class="mt-2 mb-2 b-b-info">
                                            Description
                                        </h4>

                                        <p class="mb-2">
                                            {!! $record->description !!}
                                        </p>
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
