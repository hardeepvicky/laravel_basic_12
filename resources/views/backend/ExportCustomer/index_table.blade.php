<div class="card" id="page-summary">
    <div class="card-header p-2">
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-bordered summary-table summary_table_with_search">
                <thead>
                    <tr>
                        <th style="width:10%">
                            <span class="btn btn-light btn-sm text-dark border-primary clear_search_conditions mb-1">Clear</span>
                            <?php
                            $field = 'name';
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm" />
                        </th>
                        <th style="width:15%">
                            <?php
                            $field = 'assign_to_user_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]"
                                :list="$user_list" :value="$search[$field]" pleaseSelect="All" class="select2 form-select-sm" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'status_type';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" :list="$status_type_list" :value="$search[$field]" pleaseSelect="All" class="select2 form-select-sm" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'response_type_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" :list="$lead_history_response_list" :value="$search[$field]" pleaseSelect="All" class="select2 form-select-sm" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'country_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" :list="$country_list" :value="$search[$field]" pleaseSelect="All" class="select2 form-select-sm" />
                        </th>
                        <th style="width:15%">
                            <?php
                            $field = 'state_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" :list="$state_list" :value="$search[$field]" pleaseSelect="All" class="select2 form-select-sm  mb-2" />

                            <?php
                            $field = 'city_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" :list="$city_list" :value="$search[$field]" pleaseSelect="All" class="select2 form-select-sm" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'mobile';
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" class="form-control-sm"/>
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'email';
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" class="form-control-sm"/>
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'dealer_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" :list="$dealer_list" :value="$search[$field]" pleaseSelect="All" class="select2 form-select-sm"/>
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'deal_type';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" :list="$deal_type_list" :value="$search[$field]" pleaseSelect="All" class="select2 form-select-sm" />
                        </th>
                    </tr>
                    <tr class="border-bottom-primary">
                        <th>
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            | <?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?>
                        </th>
                        <th><?= sortable_anchor('assignTo.person_name', get_value_from_list($field_labels, 'assign_to_user_id', 'assign_to_user_id')) ?></th>
                        <th><?= sortable_anchor('status_type', get_value_from_list($field_labels, 'status_type', 'status_type')) ?>
                        <th><?= sortable_anchor('response_type_id', get_value_from_list($field_labels, 'response_type_id', 'response_type_id')) ?></th>
                        <th>
                            <?= sortable_anchor('country.name', get_value_from_list($field_labels, 'country_id', 'country_id')) ?>
                        </th>
                        <th>
                            <?= sortable_anchor('state.name', get_value_from_list($field_labels, 'state_id', 'state_id')) ?>
                            |
                            <?= sortable_anchor('city.name', get_value_from_list($field_labels, 'city_id', 'city_id')) ?>
                        </th>
                        <th><?= sortable_anchor('mobile', get_value_from_list($field_labels, 'mobile', 'mobile')) ?></th>
                        <th><?= sortable_anchor('email', get_value_from_list($field_labels, 'email', 'email')) ?></th>
                        <th><?= sortable_anchor('dealer_id', get_value_from_list($field_labels, 'dealer_id', 'dealer_id')) ?></th>
                        <th><?= sortable_anchor('deal_type', get_value_from_list($field_labels, 'deal_type', 'deal_type')) ?></th>
                        <th style="width:10%">
                            <?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?>
                        </th>
                        <th style="width:10%">
                            <?= sortable_anchor('updated_by', get_value_from_list($field_labels, 'updated_by', 'updated_by')) ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <th scope="row">
                                <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix"
                                    buttons="history,edit,copy,delete,tr_details">
                                    <div class="label">
                                        {{ $record->id . ' | ' . $record->name }}
                                    </div>
                                </x-Backend.celec-summary-table-first-column>
                            </th>
                            <td>{{ $record->assignTo?->getDisplayName() }}</td>
                            <td>{{ get_value_from_list($status_type_list, $record->status_type) }}</td>
                            <td>{{ get_value_from_list($lead_history_response_list, $record->response_type_id) }}</td>
                            <td>
                                {{ $record->country?->emoji }} {{ $record->country?->getShortName() }}
                            </td>
                            <td>
                                {{ $record->state?->getShortName() }}
                                >> {{ $record->city?->getDisplayName() }}
                            </td>
                            <td>{{ $record->mobile }}</td>
                            <td>{{ $record->email }}</td>
                            <td>{{ get_value_from_list($dealer_list, $record->dealer_id) }}</td>
                            <td>{{ get_value_from_list($deal_type_list, $record->deal_type) }}</td>
                            <td>
                                <x-Backend.celec-date-time :value="$record->updated_at"/>
                            </td>
                            <td>
                                {{ get_value_from_list($userListCache, $record->updated_by) }}
                            </td>
                        </tr>
                        <tr id="record-{{ $record->id }}" class="hidden">
                            <td style="background-color: var(--body--bs-body-bg)"></td>
                            <td colspan="12">
                                <div class="row">
                                    <div class="col-md-6 col-xl-3">
                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'email') }} :
                                            <span>
                                                {{ $record->email }}
                                            </span>
                                        </label>

                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'company_name') }} :
                                            <span>
                                                {{ $record->company_name }}
                                            </span>
                                        </label>

                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'brand_name') }} :
                                            <span>
                                                {{ $record->brand_name }}
                                            </span>
                                        </label>

                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'company_gst_no') }} :
                                            <span>
                                                {{ $record->company_gst_no }}
                                            </span>
                                        </label>

                                        <h4 class="mt-2 mb-2 b-b-info">
                                            Remarks
                                        </h4>

                                        <p class="mb-2">
                                            {!! $record->remarks !!}
                                        </p>
                                    </div>
                                    <div class="col-md-6 col-xl-6">
                                        <div class="card">
                                            <div class="card-header border-l-secondary card-no-border">
                                                <h3>Products</h3>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm summary-table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>{{ get_value_from_list($product_field_labels, 'product_id') }}
                                                            </th>
                                                            <th>{{ get_value_from_list($product_field_labels, 'qty') }}
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($record->lead->products as $i => $lead_product)
                                                            <tr>
                                                                <td>{{ $i + 1 }}</td>
                                                                <td>{{ $lead_product->product?->getDisplayName() }}
                                                                </td>
                                                                <td>{{ $lead_product->qty }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="card">
                                            <div class="card-header border-l-secondary card-no-border">
                                                <h3>Address</h3>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm summary-table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>{{ get_value_from_list($address_field_labels, 'type') }}
                                                            </th>
                                                            <th>{{ get_value_from_list($address_field_labels, 'addr1') }}
                                                            </th>
                                                            <th>{{ get_value_from_list($address_field_labels, 'addr2') }}
                                                            </th>
                                                            <th>{{ get_value_from_list($address_field_labels, 'pincode') }}
                                                            </th>
                                                            <th>{{ get_value_from_list($address_field_labels, 'is_primary') }}
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($record->lead->address as $i => $address)
                                                            <tr>
                                                                <td>{{ $i + 1 }}</td>
                                                                <td>{{ get_value_from_list($address_type_list, $address->type, $address->type) }}
                                                                </td>
                                                                <td>{{ $address->addr1 }}</td>
                                                                <td>{{ $address->addr2 }}</td>
                                                                <td>{{ $address->pincode }}</td>
                                                                <td>{{ $address->is_primary ? 'Primary' : 'No' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <x-Backend.document-files :records="$record->lead->documentFile" />
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
