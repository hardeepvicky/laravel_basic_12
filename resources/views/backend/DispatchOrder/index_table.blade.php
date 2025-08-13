<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-bordered summary-table summary_table_with_search">
                <thead>
                    <tr>
                        <th>
                            <?php
                            $field = "type";
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]"/>
                        </th>                        
                    </tr>
                    <tr class="border-bottom-primary">
                        <th style="width:10%">
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            | <?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?>
                        </th>
                        <th><?= sortable_anchor('status_type', get_value_from_list($field_labels, 'status_type', 'status_type')) ?></th>
                        <th><?= sortable_anchor('carrier.name', get_value_from_list($field_labels, 'carrier_id', 'carrier_id')) ?></th>
                        <th><?= sortable_anchor('country.name', get_value_from_list($field_labels, 'country_id', 'country_id')) ?></th>
                        <th><?= sortable_anchor('state.name', get_value_from_list($field_labels, 'state_id', 'state_id')) ?></th>
                        <th><?= sortable_anchor('distirct.name', get_value_from_list($field_labels, 'district_id', 'district_id')) ?></th>
                        <th><?= sortable_anchor('city.name', get_value_from_list($field_labels, 'city_id', 'city_id')) ?></th>
                        <th><?= sortable_anchor('docket_no', get_value_from_list($field_labels, 'docket_no', 'docket_no')) ?></th>
                        <th><?= sortable_anchor('tracking_no', get_value_from_list($field_labels, 'tracking_no', 'tracking_no')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('created_at', get_value_from_list($field_labels, 'created_at', 'created_at')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <th scope="row">
                                <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" buttons="tr_details">
                                    <div class="label">
                                        {{ $record->id . " | " . $record->type }}
                                    </div>
                                </x-Backend.celec-summary-table-first-column>
                            </th>
                            <td>{{ get_value_from_list($status_type_list, $record->status_type) }}</td>
                            <td>{{ $record->carrier?->getDisplayName() }}</td>
                            <td>{{ $record->country?->getDisplayName() }}</td>
                            <td>{{ $record->state?->getDisplayName() }}</td>
                            <td>{{ $record->district?->getDisplayName() }}</td>
                            <td>{{ $record->city?->getDisplayName() }}</td>
                            <td>{{ $record->docket_no }}</td>
                            <td>{{ $record->tracking_no }}</td>
                            <td>
                                <x-Backend.created-and-by :datetime="$record->created_at" :userId="$record->created_by" />
                            </td>
                            <td>
                                <x-Backend.created-and-by :datetime="$record->updated_at" :userId="$record->updated_by" />
                            </td>
                        </tr>
                        <tr id="record-{{ $record->id }}" class="hidden">
                            <td style="background-color: var(--body--bs-body-bg)"></td>                            
                            <td colspan="9">
                                <div class="row">
                                    <div class="col-md-6">
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
