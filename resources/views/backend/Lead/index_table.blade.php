<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="m-2">
            <a href="{{ route($routePrefix . '.export.csv') }}" class="btn btn-secondary">Export CSV</a>
            <a href="{{ route($routePrefix . '.export.csv_history') }}" class="btn btn-secondary">Export History CSV</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered summary-table summary_table_with_search">
                <thead>
                    <tr>
                        <th style="width:10%">
                            <span
                                class="btn btn-light text-dark border-primary clear_search_conditions mb-1">Clear</span>
                            <?php
                            $field = 'name';
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'assign_to_user_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]"
                                :list="$user_list" :value="$search[$field]" class="select2" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'status_type';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" :list="$status_type_list" :value="$search[$field]" class="select2" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'response_type_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" :list="$lead_history_response_list" :value="$search[$field]" class="select2" />
                        </th>
                        <th style="width:12%">
                            <?php
                            $field = 'state_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" :list="$state_list" :value="$search[$field]" class="select2 mb-1" />

                            <?php
                            $field = 'city_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" :list="$district_city_list" :value="$search[$field]" class="select2" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'mobile';
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'mobile2';
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'source_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" :list="$source_list" :value="$search[$field]" class="select2" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'source_type';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" :list="$source_type_list" :value="$search[$field]" class="select2" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'dealer_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" :list="$dealer_list" :value="$search[$field]" class="select2" />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'deal_type';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" :list="$deal_type_list" :value="$search[$field]" class="select2" />
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
                            <?= sortable_anchor('state.name', get_value_from_list($field_labels, 'state_id', 'state_id')) ?>
                            |
                            <?= sortable_anchor('district.name', get_value_from_list($field_labels, 'district_id', 'district_id')) ?>
                            |
                            <?= sortable_anchor('city.name', get_value_from_list($field_labels, 'city_id', 'city_id')) ?>
                        </th>
                        <th><?= sortable_anchor('mobile', get_value_from_list($field_labels, 'mobile', 'mobile')) ?></th>
                        <th><?= sortable_anchor('mobile2', get_value_from_list($field_labels, 'mobile2', 'mobile2')) ?></th>
                        <th><?= sortable_anchor('source_id', get_value_from_list($field_labels, 'source_id', 'source_id')) ?></th>
                        <th><?= sortable_anchor('source_type', get_value_from_list($field_labels, 'source_type', 'source_type')) ?></th>
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
                                {{ $record->state?->getShortName() }}
                                >> {{ $record->district?->getDisplayName() }}
                                >> {{ $record->city?->getDisplayName() }}
                            </td>
                            <td>{{ $record->mobile }}</td>
                            <td>{{ $record->mobile2 }}</td>
                            <td>{{ get_value_from_list($source_list, $record->source_id) }}</td>
                            <td>{{ get_value_from_list($source_type_list, $record->source_type) }}</td>
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
                                            {{ get_value_from_list($field_labels, 'url') }} :
                                            <span>
                                                {{ $record->url }}
                                            </span>
                                        </label>

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

                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'campaign_id') }} :
                                            <span>
                                                {{ $record->campaign?->getDisplayName() }}
                                            </span>
                                        </label>

                                        <h4 class="mt-2 mb-2 b-b-info">
                                            Remarks
                                        </h4>

                                        <p class="mb-2">
                                            {!! $record->remarks !!}
                                        </p>
                                    </div>
                                    <div class="col-md-6 col-xl-3">

                                        @if ($record->image_url)
                                            <div class="file-content mt-2">
                                                <ul class="d-flex flex-row files-content">
                                                    <li class="folder-box d-flex align-items-center">
                                                        <div class="file-block card">
                                                            <i class="fas fa-check check-icon"></i>
                                                            <div class="card-header">
                                                                <a href="{{ $record->image_url }}"
                                                                    class="fancybox image-container"
                                                                    data-fancybox="image">
                                                                    <img class="full-img lazy-load-image"
                                                                        src="{{ $record->image_url }}" />
                                                                    <div class="image-loader"></div>
                                                                    <div class="error-icon" aria-hidden="true">
                                                                        &#x26A0;</div>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        @endif

                                        @if ($record->video_url)
                                            <div class="file-content mt-2">
                                                <ul class="d-flex flex-row files-content">
                                                    <li class="folder-box d-flex align-items-center">
                                                        <div class="file-block card">
                                                            <i class="fas fa-check check-icon"></i>
                                                            <div class="card-header">
                                                                <video class="full-video" controls src="{{ $record->video_url }}"></video>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        @endif

                                        
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
                                                        @foreach ($record->products as $i => $lead_product)
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
                                                        @foreach ($record->address as $i => $address)
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
