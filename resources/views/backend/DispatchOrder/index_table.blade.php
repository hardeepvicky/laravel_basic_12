<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-bordered summary-table summary_table_with_search">
                <thead>
                    <tr>
                        <th style="width:10%">
                            <span class="btn btn-light btn-sm text-dark border-primary clear_search_conditions mb-1">Clear</span>
                        </th>
                        <th>
                            <?php
                            $field = "sale_order_no";
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" class="form-control-sm"/>
                        </th> 
                        <th style="width:10%">
                            <?php
                            $field = "contact_id";
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
                            $field = "state_id";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" :list="$state_list" :value="$search[$field]" 
                                pleaseSelect="All" class="select2 form-select-sm mb-1" />

                            <?php
                            $field = "city_id";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" :list="$district_city_list" :value="$search[$field]" 
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
                            $field = "builty_no";
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
                        <th><?= get_value_from_list($field_labels, 'sale_order_id', 'sale_order_id') ?></th>
                        <th><?= get_value_from_list($field_labels, 'contact_id', 'contact_id') ?></th>
                        <th><?= sortable_anchor('status_type', get_value_from_list($field_labels, 'status_type', 'status_type')) ?></th>
                        <th>
                            <?= sortable_anchor('state.name', get_value_from_list($field_labels, 'state_id', 'state_id')) ?>
                            | <?= sortable_anchor('district.name', get_value_from_list($field_labels, 'district_id', 'district_id')) ?>                        
                            | <?= sortable_anchor('city.name', get_value_from_list($field_labels, 'city_id', 'city_id')) ?>
                        </th>
                        <th><?= sortable_anchor('carrier.name', get_value_from_list($field_labels, 'carrier_id', 'carrier_id')) ?></th>
                        <th><?= sortable_anchor('docket_no', get_value_from_list($field_labels, 'docket_no', 'docket_no')) ?></th>
                        <th><?= sortable_anchor('tracking_no', get_value_from_list($field_labels, 'tracking_no', 'tracking_no')) ?></th>
                        <th><?= sortable_anchor('builty_no', get_value_from_list($field_labels, 'builty_no', 'builty_no')) ?></th>
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
                                        | <a class="btn btn-sm btn-secondary" href="{{ route($routePrefix . '.send', [$record->id]) }}">Send</a>
                                    </div>
                                </x-Backend.celec-summary-table-first-column>
                            </th>
                            <td>
                                <a href="{{ route('sale-order.index', ['id' => $record->sale_order_id]) }}" target="_blank">
                                    {{ $record->saleOrder?->getDisplayName() }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('contact.index', ['id' => $record->contact_id]) }}" target="_blank">
                                    {{ $record->contact?->getDisplayName() }}
                                </a>
                            </td>
                            <td>{{ get_value_from_list($status_type_list, $record->status_type) }}</td>
                            <td>
                                {{ $record->state?->getShortName() }}
                                >> {{ $record->district?->getDisplayName() }}
                                >> {{ $record->city?->getDisplayName() }}
                            </td>
                            <td>{{ $record->carrier?->getDisplayName() }}</td>
                            <td>{{ $record->docket_no }}</td>
                            <td>{{ $record->tracking_no }}</td>
                            <td>{{ $record->builty_no }}</td>
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
                                            {{ get_value_from_list($field_labels, 'builty_no') }} :
                                            <span>
                                                {{ $record->builty_no }}
                                            </span>
                                        </label>

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

                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'shipping_fee_gst') }} :
                                            <span>
                                                {{ $record->shipping_fee_gst }} ({{ $record->shipping_fee_gst_per }} % )
                                            </span>
                                        </label>

                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'total_fee') }} :
                                            <span>
                                                {{ $record->total_fee }}
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
                                                        @foreach ($record->products as $i => $dispatchOrderProduct)
                                                            <tr>
                                                                <td>{{ $i + 1 }}</td>
                                                                <td>{{ $dispatchOrderProduct->product?->category->getDisplayName() }}</td>
                                                                <td>{{ $dispatchOrderProduct->product?->getDisplayName() }}</td>     
                                                                <td>{{ $dispatchOrderProduct->qty }}</td>                                                           
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="card">
                                            <div class="card-header border-l-secondary card-no-border">
                                                <h3>Items</h3>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm summary-table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>Category</th>
                                                            <th>Item</th>
                                                            <th>Qty</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($record->items as $i => $dispatchOrderItem)
                                                            <tr>
                                                                <td>{{ $i + 1 }}</td>
                                                                <td>{{ $dispatchOrderItem->item?->category->getDisplayName() }}</td>
                                                                <td>{{ $dispatchOrderItem->item?->getDisplayName() }}</td>
                                                                <td>{{ $dispatchOrderItem->qty }}</td>
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
