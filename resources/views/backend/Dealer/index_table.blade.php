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
                        <th style="width:10%">
                            <span class="btn btn-light text-dark border-primary clear_search_conditions mb-1">Clear</span>
                            <?php
                            $field = "name";
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$search[$field]"/>
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = "type";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" :list="$type_list" :value="$search[$field]" class="select2" />
                        </th>
                        <th style="width:12%">
                            <?php
                            $field = "state_id";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" :list="$state_list" :value="$search[$field]" class="select2 mb-1"/>

                            <?php
                            $field = "city_id";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" :list="$district_city_list" :value="$search[$field]" class="select2" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = "company_name";
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$search[$field]"/>
                        </th>                      
                        <th style="width:10%">
                            <?php
                            $field = "mobile";
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$search[$field]"/>
                        </th>                      
                        <th style="width:10%">
                            <?php
                            $field = "mobile2";
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$search[$field]"/>
                        </th>                      
                        <th style="width:10%">
                            <?php
                            $field = "phone_no";
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$search[$field]"/>
                        </th>                      
                    </tr>
                    <tr class="border-bottom-primary">
                        <th>
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            | <?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?>
                        </th>
                        <th><?= sortable_anchor('type', get_value_from_list($field_labels, 'type', 'type')) ?></th>
                        <th>
                            <?= sortable_anchor('state.name', get_value_from_list($field_labels, 'state_id', 'state_id')) ?>
                            | <?= sortable_anchor('district.name', get_value_from_list($field_labels, 'district_id', 'district_id')) ?>
                            | <?= sortable_anchor('city.name', get_value_from_list($field_labels, 'city_id', 'city_id')) ?>
                        </th>
                        <th><?= sortable_anchor('company_name', get_value_from_list($field_labels, 'company_name', 'company_name')) ?></th>
                        <th><?= sortable_anchor('mobile', get_value_from_list($field_labels, 'mobile', 'mobile')) ?></th>
                        <th><?= sortable_anchor('mobile2', get_value_from_list($field_labels, 'mobile2', 'mobile2')) ?></th>
                        <th><?= sortable_anchor('phone_no', get_value_from_list($field_labels, 'phone_no', 'phone_no')) ?></th>
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
                            <td>{{ $record->company_name }}</td>
                            <td>{{ $record->mobile }}</td>
                            <td>{{ $record->mobile2 }}</td>
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
                            <td colspan="11">
                                <div class="row">
                                    <div class="col-md-6 col-xl-3">
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
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card">
                                            <div class="card-header border-l-secondary card-no-border">
                                                <h3>Terms</h3>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm summary-table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>{{ get_value_from_list($term_field_labels, 'name') }}</th>
                                                            <th>{{ get_value_from_list($term_field_labels, 'is_active') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($record->terms as $i => $term)
                                                        <tr>
                                                            <td>{{ $i + 1 }}</td>
                                                            <td>{{ $term->name }}</td>
                                                            <td>{{ $term->is_active ? "Active" : "No" }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>     
                                    </div>
                                    <div class="col-md-6 col-xl-5">
                                        <div class="card">
                                            <div class="card-header border-l-secondary card-no-border">
                                                <h3>Address</h3>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm summary-table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>{{ get_value_from_list($address_field_labels, 'type') }}</th>
                                                            <th>{{ get_value_from_list($address_field_labels, 'addr1') }}</th>
                                                            <th>{{ get_value_from_list($address_field_labels, 'addr2') }}</th>
                                                            <th>{{ get_value_from_list($address_field_labels, 'pincode') }}</th>
                                                            <th>{{ get_value_from_list($address_field_labels, 'is_primary') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($record->address as $i => $address)
                                                        <tr>
                                                            <td>{{ $i + 1 }}</td>
                                                            <td>{{ get_value_from_list($address_type_list, $address->type, $address->type) }}</td>
                                                            <td>{{ $address->addr1 }}</td>
                                                            <td>{{ $address->addr2 }}</td>
                                                            <td>{{ $address->pincode }}</td>
                                                            <td>{{ $address->is_primary ? "Primary" : "No" }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>                                          
                                    </div>
                                </div>

                                <x-Backend.document-files :records="$record->documentFile" />
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
