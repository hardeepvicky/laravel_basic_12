<div class="card" id="page-summary">
    <div class="card-header">
        <a href="{{ route($routePrefix . '.export.csv') }}" class="btn btn-secondary">Export CSV</a>
        <a href="{{ route($routePrefix . '.export.product_csv') }}" class="btn btn-secondary">Export Product CSV</a>
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-bordered summary-table summary_table_with_search">
                <thead>
                    <tr>
                        <th style="width:10%">
                            <span class="btn btn-light btn-sm text-dark border-primary clear_search_conditions mb-1">Clear</span>
                        </th>
                        <th></th>
                        <th>
                            <?php
                            $field = "export_order_no";
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" class="form-control-sm"/>
                        </th> 
                        <th style="width:10%">
                            <?php
                            $field = "export_contact_id";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" :list="$contact_list" :value="$search[$field]" 
                                pleaseSelect="All" class="select2 form-select-sm"  />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = "status_type";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" :list="$status_type_list" :value="$search[$field]" 
                                pleaseSelect="All" class="select2 form-select-sm" />
                        </th>
                        <th style="width:12%">                            
                            <?php
                            $field = "country_id";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" :list="$country_list" :value="$search[$field]" 
                                pleaseSelect="All" class="select2 form-select-sm mb-1" />

                            <?php
                            $field = "state_id";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" :list="$state_list" :value="$search[$field]" 
                                pleaseSelect="All" class="select2 form-select-sm mb-1" />

                            <?php
                            $field = "city_id";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" :list="$city_list" :value="$search[$field]" 
                                pleaseSelect="All" class="select2 form-select-sm" />
                        </th>
                        <th style="width:12%">
                            <?php
                            $field = "carrier_id";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" :list="$carrier_list" :value="$search[$field]" 
                                pleaseSelect="All" class="select2 form-select-sm"/>
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = "docket_no";
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm"/>
                        </th>               
                        <th style="width:10%">
                            <?php
                            $field = "tracking_no";
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm"/>
                        </th>      
                        <th style="width:10%">
                            <?php
                            $field = 'from_date';
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm date-picker" />
                            <?php
                            $field = 'to_date';
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm date-picker" />
                        </th>         
                    </tr>
                    <tr class="border-bottom-primary">
                        <th style="width:10%">
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                        </th>
                        <th><?= get_value_from_list($field_labels, 'order_from_type', 'order_from_type') ?></th>
                        <th><?= get_value_from_list($field_labels, 'export_order_id', 'export_order_id') ?></th>
                        <th><?= get_value_from_list($field_labels, 'export_contact_id', 'export_contact_id') ?></th>
                        <th><?= sortable_anchor('status_type', get_value_from_list($field_labels, 'status_type', 'status_type')) ?></th>
                        <th>
                            <?= sortable_anchor('country.name', get_value_from_list($field_labels, 'country_id', 'country_id')) ?>                        
                            | <?= sortable_anchor('state.name', get_value_from_list($field_labels, 'state_id', 'state_id')) ?>
                            | <?= sortable_anchor('city.name', get_value_from_list($field_labels, 'city_id', 'city_id')) ?>
                        </th>
                        <th><?= sortable_anchor('carrier.name', get_value_from_list($field_labels, 'carrier_id', 'carrier_id')) ?></th>
                        <th><?= sortable_anchor('docket_no', get_value_from_list($field_labels, 'docket_no', 'docket_no')) ?></th>
                        <th><?= sortable_anchor('tracking_no', get_value_from_list($field_labels, 'tracking_no', 'tracking_no')) ?></th>
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
                                <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" buttons="edit,tr_details">
                                    <div class="label">
                                        {{ $record->id}} 
                                        | <a class="btn btn-sm btn-secondary" href="{{ route($routePrefix . '.send', [$record->id]) }}">Scan</a>
                                    </div>
                                </x-Backend.celec-summary-table-first-column>
                            </th>
                            <td>
                                {{ $record->orderFrom() }}
                            </td>
                            <td>
                                <a href="{{ route('export-order.index', ['id' => $record->export_order_id]) }}" target="_blank">
                                    {{ $record->exportOrder?->getDisplayName() }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('export-contact.index', ['id' => $record->export_contact_id]) }}" target="_blank">
                                    {{ $record->contact?->getDisplayName() }}
                                </a>
                            </td>
                            <td>{{ get_value_from_list($status_type_list, $record->status_type) }}</td>
                            <td>
                                {{ $record->country?->emoji }}
                                {{ $record->country?->getDisplayName() }}
                                >> {{ $record->state?->getShortName() }}
                                >> {{ $record->city?->getDisplayName() }}
                            </td>
                            <td>{{ $record->carrier?->getDisplayName() }}</td>
                            <td>{{ $record->docket_no }}</td>
                            <td>{{ $record->tracking_no }}</td>
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
                                    <div class="col-md-5">
                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'tracking_phone_no') }} :
                                            <span>
                                                {{ $record->tracking_phone_no }}
                                            </span>
                                        </label>

                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'shipping_fee') }} :
                                            <span>
                                                {{ $record->shipping_fee }}
                                            </span>
                                        </label>

                                        <h4 class="mt-2 mb-2 b-b-info">
                                            Remarks
                                        </h4>

                                        <p class="mb-2">
                                            {!! $record->remarks !!}
                                        </p>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="card">
                                            <div class="card-header border-l-secondary card-no-border">
                                                <h3>Products</h3>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm summary-table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>Category</th>
                                                            <th>Model No.</th>
                                                            <th>Qty</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($record->shipOrderProduct as $i => $shipOrderProduct)
                                                            <tr>
                                                                <td>{{ $i + 1 }}</td>
                                                                <td>{{ $shipOrderProduct->product?->category->getDisplayName() }}</td>
                                                                <td>{{ $shipOrderProduct->product?->getDisplayName() }}</td>     
                                                                <td>{{ $shipOrderProduct->qty }}</td>                                                           
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
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
