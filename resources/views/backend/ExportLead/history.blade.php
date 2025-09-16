<?php

use App\Models\Lead;
use App\Models\LeadHistory;
use App\Helpers\DateUtility;

?>

@extends($layout)

@section('content')
    <?php
    $page_header_links = [
        ['title' => 'Edit', 'url' => route($routePrefix . '.edit', [$record])],
        ['title' => 'List', 'url' => route($routePrefix . '.index')],
    ];
    ?>

    @include($partial_path . '.page_header')

    @include($viewPrefix . '.show')

    <x-Backend.form-errors />

    <div class="card">
        <div class="card-header card-no-border pb-0">
            History

            <a class="btn btn-secondary call_link" href="tel:+{{ $record->country->phonecode }}{{ $record->mobile }}">Call</a>
            <div class="btn btn-secondary sms_link">SMS</div>
            <a class="btn btn-secondary whatsapp_link" href="https://web.whatsapp.com/" target="_blank" rel="noopener noreferrer">
                Whatsapp
            </a>
            <div class="btn btn-secondary email_link">Email</div>
            <div class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modal-snooze-form">Snooze</div>
            <div class="btn btn-secondary dispatch_link" data-bs-toggle="modal" data-bs-target="#modal-dispatch-form">Dispatch</div>
            <div class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modal-transfer-form">Transfer</div>
            @if($record->exp_lead_id)
                <a class="btn btn-secondary confirm" href="{{ route("export-customer.convert_to_lead", [$record->exp_lead_id])  }}">
                    Convert To Lead
                </a>          
            @elseif(isset($record->is_customer) && !$record->is_customer)
                <a class="btn btn-secondary confirm" href="{{ route("export-lead.convert_to_customer", [$record->id])  }}">
                    Convert To Customer
                </a>
            @endif

            <div class="card-header-right">
                <ul class="list-unstyled card-option" style="width: 230px;">
                    <li><i class="fa-solid fa-maximize full-card"></i></li>
                    <li><i class="fa-solid fa-minus minimize-card"></i></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered summary-table">
                    <thead>
                        <tr class="border-bottom-primary">
                            <th>Sr. No.</th>
                            <th style="width:10%">{{ get_value_from_list($history_field_labels, 'created_at', 'created_at') }}</th>
                            <th><?= get_value_from_list($history_field_labels, 'entry_by', 'entry_by') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'action_type', 'action_type') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'action', 'action') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'response_type_id', 'response_type_id') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'response', 'response') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'recording_file', 'recording_file') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'remarks', 'remarks') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'status_type', 'status_type') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'assign_to_user_id', 'assign_to_user_id') ?></th>
                            <th style="width:10%"><?= get_value_from_list($history_field_labels, 'next_followup_date_time', 'next_followup_date_time') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($histories as $i => $history)
                        <?php
                            $history->afterFind();
                        ?>
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <x-Backend.created-and-by :datetime="$history->created_at" :userId="$history->created_by" />
                            </td>
                            <td>{{ $history->entry_by }}</td>
                            <td>{{ get_value_from_list($action_type_list, $history->action_type) }}</td>
                            <td>
                                <div class="more-less-text">{{ $history->action }}</div>
                            </td>
                            <td>
                                {{ get_value_from_list($lead_history_response_list, $history->response_type_id) }}
                            </td>
                            <td>
                                <div class="more-less-text">{{ $history->response }}</div>
                            </td>
                            <td>
                                <?php
                                    $mime_type = get_mime_type_from_extension(pathinfo($history->recording_file,PATHINFO_EXTENSION));
                                ?>
                                <audio controls>
                                    <source src="{{ $history->recording_file_url }}" type="{{ $mime_type }}">
                                    Your browser does not support the audio element.
                                </audio>
                            </td>
                            <td>
                                <div class="more-less-text">{{ $history->remarks }}</div>
                            </td>
                            <td>
                                {{ get_value_from_list($status_type_list, $history->status_type) }}
                            </td>
                            <td>
                                {{ get_value_from_list($userListCache, $history->assign_to_user_id) }}
                            </td>
                            <td>
                                <x-Backend.celec-date-time :value="$history->next_followup_date_time" :utcDate="$history->next_followup_date_time_original" :timezone="$record->state?->timezone"/>
                            </td>
                        </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-sms-whatsapp-email-form" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5"></h3>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="height: 80vh; overflow-y:scroll">
                    <x-form.start action="{{ route('export-lead.history_store', [$record->exp_lead_id ?? $record->id]) }}" method="POST" />
                        <x-Inputs.text-field type="hidden" name="entry_by" :label="$false" value="MANUAL" />
                        <x-Inputs.text-field type="hidden" name="action_type" :label="$false" value="" class="action_type" />
                        <div class="form-group mb-3">
                            <?php
                            $field = 'action';
                            $label = get_value_from_list($history_field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-area :id="$field" :name="$field" :label="$label" rows="3" mandatory="true" />
                        </div>
                        <div class="form-group mb-3">
                            <?php
                            $field = 'response_type_id';
                            $label = get_value_from_list($history_field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                :list="$lead_history_response_list" class="modal-select2"
                                />
                        </div>
                        <div class="form-group mb-3">
                            <?php
                            $field = 'response';
                            $label = get_value_from_list($history_field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-area :id="$field" :name="$field" :label="$label" rows="3"/>
                        </div>
                        <div class="form-group mb-3">
                            <?php
                            $field = 'status_type';
                            $label = get_value_from_list($history_field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                :list="$status_type_list_for_form" :value="$record->{$field}" class="modal-select2" mandatory="true"
                                />
                        </div>
                        <div class="form-group mb-3">
                            <?php
                            $field = 'remarks';
                            $label = get_value_from_list($history_field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-area :id="$field" :name="$field" :label="$label" rows="3"/>
                        </div>
                        <div class="form-group mb-3">
                            <?php
                            $field = 'next_followup_date_time';
                            $label = get_value_from_list($history_field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$label" class="next-date-time-picker"/>
                            Other Country Time : 
                            <span class="other_country_time" id="other_country_time"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-start">
                    <button class="btn btn-secondary closed" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary submit" type="button">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-transfer-form" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5">Tranfer To Another User</h3>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="height: 40vh; overflow-y:scroll">
                    <x-form.start action="{{ route('export-lead.transfer_lead', [$record->exp_lead_id ?? $record->id]) }}" method="POST" />
                        <x-Inputs.text-field type="hidden" name="entry_by" :label="$false" value="MANUAL" />
                        <x-Inputs.text-field type="hidden" name="action_type" :label="$false" value="{{ LeadHistory::ACTION_TYPE_LEAD_TRANSFER }}" />
                        <div class="form-group mb-3">
                            <?php
                            $field = 'assign_to_user_id';
                            $label = get_value_from_list($history_field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                :list="$user_list" class="modal-select2"
                                mandatory="true" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-start">
                    <button class="btn btn-secondary closed" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary submit" type="button">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-snooze-form" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5">Snooze</h3>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="height: 40vh; overflow-y:scroll">
                    <x-form.start action="{{ route($routePrefix . '.snooze', [$record->id]) }}" method="POST" />
                        <div class="form-group mb-3">
                            <?php
                            $field = 'snooze_minute';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.group-text-field :name="$field" :label="$label"
                                class="validate-int"
                                placeholder="Enter {{ $label }}"
                                secondaryText='<span class="input-group-text">Minutes</span>'
                                secondaryTextPosition="last" mandatory="true" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-start">
                    <button class="btn btn-secondary closed" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary submit" type="button">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-dispatch-form" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog  modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5">Dispatch</h3>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="height: 80vh; overflow-y:scroll">
                    <x-form.start action="{{ route($routePrefix . '.dispatch', [$record->id]) }}" method="POST" />
                        <x-Inputs.text-field type="hidden" name="entry_by" :label="$false" value="MANUAL" />
                        <x-Inputs.text-field type="hidden" name="action_type" :label="$false" value="{{ LeadHistory::ACTION_TYPE_DISPATCH }}" />
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <?php
                                    $field = 'status_type';
                                    $label = get_value_from_list($history_field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                        :list="$status_type_list_for_form" :value="$record->{$field}" class="modal-select2" mandatory="true"
                                        />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <?php
                                    $field = 'next_followup_date_time';
                                    $label = get_value_from_list($history_field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.text-field :id="$field" :name="$field" :label="$label" class="next-date-time-picker"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <?php
                                    $field = 'purpose_price';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.text-field :id="$field" :name="$field" :label="$label" :value="$record->{$field}"  mandatory="true"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <?php
                                    $field = 'shipping_fee';
                                    $label = "Shipping Fee";
                                    ?>
                                    <x-Inputs.text-field :id="$field" :name="$field" :label="$label" class="validate-float"  mandatory="true"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <?php
                                    $field = 'payable_amount';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.text-field :id="$field" :name="$field" :label="$label" :value="$record->{$field}" class="validate-float"  mandatory="true" readonly="readonly"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <?php
                                    $field = 'payment_method_type';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                        :list="$payment_method_type_list" :value="$record->{$field}" class="modal-select2" mandatory="true"
                                        />
                                </div>
                            </div>
                            <div class="col-md-12">
                                 <div class="form-group mb-3">
                                    <?php
                                    $field = 'remarks';
                                    $label = get_value_from_list($history_field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.text-area :id="$field" :name="$field" :label="$label" rows="3"/>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h4 class="b-b-info">
                                    Send Products
                                </h4>
                                <table class="table table-bordered order-column template-table"
                                    data-sr-table-template-min-row="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width : 50px;">
                                                <span class="sr-table-template-add">
                                                    <i class="fas fa-plus-circle text-success icon"></i>
                                                </span>
                                            </th>
                                            <th style="width:40%">
                                                {{ get_value_from_list($product_field_labels, 'product_id') }}
                                            </th>
                                            <th style="width:30%">
                                                {{ get_value_from_list($product_field_labels, 'make_id') }}
                                            </th>
                                            <th style="width:20%">
                                                {{ get_value_from_list($product_field_labels, 'qty') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="sr-table-template-row hidden">
                                            <td>
                                                <div class="block">
                                                    <div class="left-block">
                                                        <div class="sr-table-template-move-up">
                                                            <i class="fas fa-arrow-circle-up icon"></i>
                                                        </div>
                                                        <div class="sr-table-template-move-down">
                                                            <i class="fas fa-arrow-circle-down icon"></i>
                                                        </div>
                                                    </div>
                                                    <div class="right-block">
                                                        <span class="sr-table-template-delete">
                                                            <i class=" fas fa-times-circle text-danger icon"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <x-inputs.text-field type="hidden" name="products[(sr-id)][id]"
                                                    label="" />
                                            </td>
                                            <td>
                                                <?php
                                                $field = 'product_id';
                                                ?>
                                                <x-inputs.drop-down name="products[(sr-id)][{{ $field }}]"
                                                    label="" :list="$product_list" class="will-required will-select2"
                                                    />
                                            </td>
                                            <td>
                                                <?php
                                                $field = 'make_id';
                                                ?>
                                                <x-inputs.drop-down name="products[(sr-id)][{{ $field }}]"
                                                    label="" :list="$make_list" class="will-required will-select2"
                                                    />
                                            </td>
                                            <td>
                                                <?php
                                                $field = 'qty';
                                                ?>
                                                <x-inputs.text-field name="products[(sr-id)][{{ $field }}]"
                                                    label="" class="will-required validate-float"/>
                                            </td>
                                        </tr>
                                        @php
                                            $products = old('products', $products ?? []);
                                            unset($products['(sr-id)']);
                                        @endphp
                                        @if (isset($products))
                                            @foreach ($products as $k => $child_arr)
                                                <tr class="" sr-id="{{ $child_arr['id'] ?? $k }}"
                                                    sr-counter="{{ $k }}">
                                                    <td>
                                                        <div class="block">
                                                            <div class="left-block">
                                                                <div class="sr-table-template-move-up">
                                                                    <i class="fas fa-arrow-circle-up icon"></i>
                                                                </div>
                                                                <div class="sr-table-template-move-down">
                                                                    <i class="fas fa-arrow-circle-down icon"></i>
                                                                </div>
                                                            </div>
                                                            <div class="right-block">
                                                                <span class="sr-table-template-delete">
                                                                    <i
                                                                        class=" fas fa-times-circle text-danger icon"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="products[{{ $k }}][id]"
                                                            value="{{ $child_arr['id'] ?? '' }}" />
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $field = 'product_id';
                                                        $value = $child_arr[$field] ?? '';
                                                        ?>
                                                        <x-inputs.drop-down name="products[{{ $k }}][{{ $field }}]"
                                                            label="" :list="$product_list" :value="$value" class="will-required will-select2"
                                                            />
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $field = 'make_id';
                                                        $value = $child_arr[$field] ?? '';
                                                        ?>
                                                        <x-inputs.drop-down name="products[{{ $k }}][{{ $field }}]"
                                                            label="" :list="$make_list" :value="$value" class="will-required will-select2"
                                                            />
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $field = 'qty';
                                                        $value = $child_arr[$field] ?? '';
                                                        ?>
                                                        <x-inputs.text-field name="products[{{ $k }}][{{ $field }}]"
                                                            label="" :value="$value" class="will-required validate-float"/>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="text-center" style="width : 80px;">
                                                <span class="sr-table-template-add">
                                                    <i class="fas fa-plus-circle text-success icon"></i>
                                                </span>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h4 class="b-b-info">
                                    Send Items
                                </h4>
                                <table class="table table-bordered order-column template-table"
                                    data-sr-table-template-min-row="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width : 50px;">
                                                <span class="sr-table-template-add">
                                                    <i class="fas fa-plus-circle text-success icon"></i>
                                                </span>
                                            </th>
                                            <th style="width:50%">
                                                {{ get_value_from_list($item_field_labels, 'item_id') }}
                                            </th>
                                            <th style="width:20%">
                                                {{ get_value_from_list($item_field_labels, 'qty') }}
                                            </th>
                                            <th style="width:30%">
                                                {{ get_value_from_list($item_field_labels, 'uom_id') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="sr-table-template-row hidden">
                                            <td>
                                                <div class="block">
                                                    <div class="left-block">
                                                        <div class="sr-table-template-move-up">
                                                            <i class="fas fa-arrow-circle-up icon"></i>
                                                        </div>
                                                        <div class="sr-table-template-move-down">
                                                            <i class="fas fa-arrow-circle-down icon"></i>
                                                        </div>
                                                    </div>
                                                    <div class="right-block">
                                                        <span class="sr-table-template-delete">
                                                            <i class=" fas fa-times-circle text-danger icon"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <x-inputs.text-field type="hidden" name="items[(sr-id)][id]"
                                                    label="" />
                                            </td>
                                            <td>
                                                <?php
                                                $field = 'item_id';
                                                ?>
                                                <x-inputs.drop-down name="items[(sr-id)][{{ $field }}]"
                                                    label="" :list="$item_list" class="will-required item_id will-select2"
                                                    data-sr-cascade-target="#lead-uom_id-(sr-id)"
                                                    data-sr-cascade-url="/item/ajax_get_child_uom_list/{v}"
                                                    />
                                            </td>
                                            <td>
                                                <?php
                                                $field = 'qty';
                                                ?>
                                                <x-inputs.text-field name="items[(sr-id)][{{ $field }}]"
                                                    label="" class="will-required validate-float"/>
                                            </td>
                                            <td>
                                                <?php
                                                $field = 'uom_id';
                                                ?>
                                                <x-inputs.drop-down id="lead-uom_id-(sr-id)" name="items[(sr-id)][{{ $field }}]"
                                                    label="" :list="$empty_list" class="will-required will-select2"
                                                    />
                                            </td>
                                        </tr>
                                        @php
                                            $items = old('items', $items ?? []);
                                            unset($items['(sr-id)']);
                                        @endphp
                                        @if (isset($items))
                                            @foreach ($items as $k => $child_arr)
                                                <tr class="" sr-id="{{ $child_arr['id'] ?? $k }}"
                                                    sr-counter="{{ $k }}">
                                                    <td>
                                                        <div class="block">
                                                            <div class="left-block">
                                                                <div class="sr-table-template-move-up">
                                                                    <i class="fas fa-arrow-circle-up icon"></i>
                                                                </div>
                                                                <div class="sr-table-template-move-down">
                                                                    <i class="fas fa-arrow-circle-down icon"></i>
                                                                </div>
                                                            </div>
                                                            <div class="right-block">
                                                                <span class="sr-table-template-delete">
                                                                    <i
                                                                        class=" fas fa-times-circle text-danger icon"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="items[{{ $k }}][id]"
                                                            value="{{ $child_arr['id'] ?? '' }}" />
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $field = 'item_id';
                                                        $value = $child_arr[$field] ?? '';
                                                        ?>
                                                        <x-inputs.drop-down name="items[{{ $k }}][{{ $field }}]"
                                                            label="" :list="$item_list" :value="$value" class="will-required item_id will-select2"
                                                            data-sr-cascade-target="#lead-uom_id-{{ $k }}"
                                                            data-sr-cascade-url="/item/ajax_get_child_uom_list/{v}"
                                                            />
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $field = 'qty';
                                                        $value = $child_arr[$field] ?? '';
                                                        ?>
                                                        <x-inputs.text-field name="items[{{ $k }}][{{ $field }}]"
                                                            label="" :value="$value" class="will-required validate-float"/>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $field = 'uom_id';
                                                        $value = $child_arr[$field] ?? '';
                                                        ?>
                                                        <x-inputs.drop-down id="lead-uom_id-{{ $k }}" name="items[{{ $k }}][{{ $field }}]"
                                                            label="" :list="$empty_list" :value="$value" class="will-required will-select2"
                                                            />
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="text-center" style="width : 80px;">
                                                <span class="sr-table-template-add">
                                                    <i class="fas fa-plus-circle text-success icon"></i>
                                                </span>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        @if ($record->deal_type == Lead::DEAL_TYPE_DIRECT)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h4 class="b-b-info">
                                    Address
                                </h4>
                                <table class="table table-bordered order-column template-table"
                                    data-sr-table-template-min-row="1">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width : 50px;">
                                                <span class="sr-table-template-add">
                                                    <i class="fas fa-plus-circle text-success icon"></i>
                                                </span>
                                            </th>
                                            <th style="width:80%">
                                                Address
                                            </th>
                                            <th style="width:10%">
                                                {{ get_value_from_list($address_field_labels, 'is_primary') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="sr-table-template-row hidden">
                                            <td>
                                                <div class="block">
                                                    <div class="left-block">
                                                        <div class="sr-table-template-move-up">
                                                            <i class="fas fa-arrow-circle-up icon"></i>
                                                        </div>
                                                        <div class="sr-table-template-move-down">
                                                            <i class="fas fa-arrow-circle-down icon"></i>
                                                        </div>
                                                    </div>
                                                    <div class="right-block">
                                                        <span class="sr-table-template-delete">
                                                            <i class=" fas fa-times-circle text-danger icon"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <x-inputs.text-field type="hidden" name="addresses[(sr-id)][id]"
                                                    label="" />
                                            </td>
                                            <td>
                                                <div class="form-group mb-2">
                                                    <?php
                                                    $field = 'type';
                                                    $label = get_value_from_list($address_field_labels, $field, $field);
                                                    ?>
                                                    <x-inputs.drop-down name="addresses[(sr-id)][{{ $field }}]"
                                                        :label="$label"
                                                        :list="$address_type_list"
                                                        class="will-required will-select2" />
                                                </div>

                                                <div class="form-group mb-2">
                                                    <?php
                                                    $field = 'addr1';
                                                    $label = get_value_from_list($address_field_labels, $field, $field);
                                                    ?>
                                                    <x-inputs.text-field name="addresses[(sr-id)][{{ $field }}]"
                                                        :label="$label" class="will-required" />
                                                </div>
                                                <div class="form-group mb-2">
                                                    <?php
                                                    $field = 'addr2';
                                                    $label = get_value_from_list($address_field_labels, $field, $field);
                                                    ?>
                                                    <x-inputs.text-field name="addresses[(sr-id)][{{ $field }}]"
                                                        :label="$label" />
                                                </div>

                                                <div class="form-group mb-2">
                                                    <?php
                                                    $field = 'pincode';
                                                    $label = get_value_from_list($address_field_labels, $field, $field);
                                                    ?>
                                                    <x-inputs.text-field name="addresses[(sr-id)][{{ $field }}]"
                                                        :label="$label" class="will-required validate-int" />
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $field = 'is_primary';
                                                ?>
                                                <x-inputs.checkbox name="addresses[(sr-id)][{{ $field }}]"
                                                    label="" class="is_primary" />
                                            </td>
                                        </tr>
                                        @php
                                            $addresses = old('addresses', $addresses ?? []);
                                            unset($addresses['(sr-id)']);
                                            // d($addresses, true);
                                        @endphp
                                        @if (isset($addresses))
                                            @foreach ($addresses as $k => $child_arr)
                                                <tr class="" sr-id="{{ $child_arr['id'] }}" sr-counter="{{ $k }}">
                                                    <td>
                                                        <div class="block">
                                                            <div class="left-block">
                                                                <div class="sr-table-template-move-up">
                                                                    <i class="fas fa-arrow-circle-up icon"></i>
                                                                </div>
                                                                <div class="sr-table-template-move-down">
                                                                    <i class="fas fa-arrow-circle-down icon"></i>
                                                                </div>
                                                            </div>
                                                            <div class="right-block">
                                                                <span class="sr-table-template-delete">
                                                                    <i
                                                                        class=" fas fa-times-circle text-danger icon"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="addresses[{{ $k }}][id]"
                                                            value="{{ $child_arr['id'] ?? '' }}" />
                                                    </td>
                                                    <td>
                                                        <div class="form-group mb-2">
                                                            <?php
                                                            $field = 'type';
                                                            $value = $child_arr[$field] ?? '';
                                                            $label = get_value_from_list($address_field_labels, $field, $field);
                                                            ?>
                                                            <x-inputs.drop-down name="addresses[{{ $k }}][{{ $field }}]"
                                                                :label="$label"
                                                                :value="$value"
                                                                :list="$address_type_list"
                                                                class="will-required will-select2" />
                                                        </div>

                                                        <div class="form-group mb-2">
                                                            <?php
                                                            $field = 'addr1';
                                                            $value = $child_arr[$field] ?? '';
                                                            $label = get_value_from_list($address_field_labels, $field, $field);
                                                            ?>
                                                            <x-inputs.text-field name="addresses[{{ $k }}][{{ $field }}]"
                                                                :label="$label" :value="$value" class="will-required" />
                                                        </div>
                                                        <div class="form-group mb-2">
                                                            <?php
                                                            $field = 'addr2';
                                                            $value = $child_arr[$field] ?? '';
                                                            $label = get_value_from_list($address_field_labels, $field, $field);
                                                            ?>
                                                            <x-inputs.text-field name="addresses[{{ $k }}][{{ $field }}]"
                                                                :label="$label" :value="$value" />
                                                        </div>

                                                        <div class="form-group mb-2">
                                                            <?php
                                                            $field = 'pincode';
                                                            $value = $child_arr[$field] ?? '';
                                                            $label = get_value_from_list($address_field_labels, $field, $field);
                                                            ?>
                                                            <x-inputs.text-field name="addresses[{{ $k }}][{{ $field }}]"
                                                                :label="$label" :value="$value" class="will-required validate-int" />
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $field = 'is_primary';
                                                        $value = $child_arr[$field] ?? '';
                                                        ?>
                                                        <x-inputs.checkbox
                                                            name="addresses[{{ $k }}][{{ $field }}]"
                                                            label="" :value="$value" class="is_primary" />
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="text-center" style="width : 80px;">
                                                <span class="sr-table-template-add">
                                                    <i class="fas fa-plus-circle text-success icon"></i>
                                                </span>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-start">
                    <button class="btn btn-secondary closed" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary submit" type="button">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function(){

            function cal_price()
            {
                var purpose_price = $("#purpose_price").val();
                purpose_price = purpose_price ? parseFloat(purpose_price): 0;

                var shipping_fee = $("#shipping_fee").val();
                shipping_fee = shipping_fee ? parseFloat(shipping_fee): 0;

                var payable_amount = purpose_price + shipping_fee;

                $("#payable_amount").val(payable_amount.toFixed(2));
            }

            function modal_form_init(modal) {
                setTimeout(() => {
                    modal.find('.modal-select2').select2({
                        placeHolder: "Please Select",
                        theme: "bootstrap-5",
                        dropdownParent: modal
                    });
                }, 500);

                next_date_time_picker = modal.find(".next-date-time-picker").flatpickr({
                    enableTime: true,
                    minDate: "today",
                    dateFormat: constants.dateTimeFormat,
                    time_24hr: true,
                    onChange: function(selectedDates, dateStr, instance) {
                        const selectedDate = selectedDates[0];
                        if (selectedDate) 
                        {
                            get_other_country_date_time(selectedDate, '{{ $record->state?->timezone }}', function(new_dt){
                                $("#other_country_time").html(new_dt);
                            });
                        }
                    }
                });

                modal.find(".template-table").srTableTemplate({
                    brace_type: "(",
                    afterRowAdd(_table, last_id, _tr) {
                        // _tr.find(".will-select2").select2({
                        //     placeHolder: "Please Select",
                        //     theme: "bootstrap-5",
                        //     dropdownParent: modal
                        // });

                        _tr.find(".item_id").cascade({
                            beforeGet: function (src, url) {
                                $.loader.init().setInfo("Fetching Uom ...").show();
                                return url;
                            },
                            afterGet: function (src, dest, response) {
                                $.loader.init().hide();
                                return response;
                            },
                        });

                        _tr.find(".will-required").attr("required", true);
                    }
                });

                var _trs = modal.find(".template-table").find("> tbody > tr").not(".sr-table-template-row");

                _trs.find(".item_id").cascade({
                    beforeGet: function (src, url) {
                        $.loader.init().setInfo("Fetching Uom ...").show();
                        return url;
                    },
                    afterGet: function (src, dest, response) {
                        $.loader.init().hide();
                        return response;
                    },
                }).trigger("change", { pageLoad : true });

                modal.find(".modal-body form").submit(function(){
                    var _this = $(this);
                    var confirm = _this.attr("data-confirm");

                    if (!confirm)
                    {
                        confirmDialog("", function(){
                            _this.attr("data-confirm", 1);
                            _this.trigger("submit");
                        })
                        return false;
                    }
                });

                modal.find(".modal-body form").ajaxForm({
                    beforeSubmit: function(arr, $form, options) {

                        $.loader.init().setInfo("Uploading....").show();

                        return true;
                    },
                    uploadProgress: function(event, position, total, percentComplete) {
                        $.loader.setInfo("Uploading " + percentComplete + "%");
                    },
                    error: function(xhr, status, errorString) {
                        $.loader.init().hide();

                        if (typeof xhr.responseText == "string")
                        {
                            response = xhr.responseText.trim();

                            if (response.length == 0) {
                                $.events.onAjaxError("JSON Parse Error", "Empty Response");
                                return false;
                            }
                        }
                        else
                        {
                            console.log(xhr.responseText);
                            return false;
                        }

                        try {
                            var responseJson = JSON.parse(response);
                        } catch (e) {
                            $.events.onAjaxError("JSON Parse Error", response);
                            return false;
                        }

                        var first_error_ele = form_errors(modal.find("form"), responseJson);
                        var modalBody = modal.find('.modal-body');
                        if (modalBody.length > 0 && first_error_ele)
                        {
                            modalBody.animate({
                                scrollTop: first_error_ele.offset().top - modalBody.offset().top + modalBody.scrollTop() - 200
                            }, 800);
                        }
                    },
                    success: function(responseText, statusText, xhr) {

                        var form_url = modal.find("form").attr("action");

                        ajaxHandleResponse(form_url, responseText, function(response) {
                            modal.find("button.closed").trigger("click");

                            $.loader.init().setInfo("Reloading....");
                            window.location.reload(true);
                        });
                    }
                });
            }

            modal_form_init($("#modal-transfer-form"));

            $('#modal-transfer-form .submit').click(function() {
                $('#modal-transfer-form form').trigger("submit");
            });


            modal_form_init($("#modal-sms-whatsapp-email-form"));

            $('#modal-sms-whatsapp-email-form .submit').click(function() {
                $('#modal-sms-whatsapp-email-form form').trigger("submit");
            });


            modal_form_init($("#modal-dispatch-form"));

            $('#modal-dispatch-form .submit').click(function() {
                $('#modal-dispatch-form form').trigger("submit");
            });

            modal_form_init($("#modal-snooze-form"));

            $('#modal-snooze-form .submit').click(function() {
                $('#modal-snooze-form form').trigger("submit");
            });

            $(".sms_link").click(function(){
                var modal = $("#modal-sms-whatsapp-email-form");

                modal.find("form").find('input:text, textarea').val('');

                modal.find(".modal-title").text("SMS");
                modal.find(".action_type").val('{{ LeadHistory::ACTION_TYPE_SEND_SMS }}');

                modal.modal("show");
            });

            $(".call_link").click(function(){
                var modal = $("#modal-sms-whatsapp-email-form");

                modal.find("form").find('input:text, textarea').val('');

                modal.find(".modal-title").text("CALL");
                modal.find(".action_type").val('{{ LeadHistory::ACTION_TYPE_CALL }}');

                modal.modal("show");
            });

            $(".whatsapp_link").click(function(){
                var modal = $("#modal-sms-whatsapp-email-form");

                modal.find("form").find('input:text, textarea').val('');

                modal.find(".modal-title").text("Whatsapp");
                modal.find(".action_type").val('{{ LeadHistory::ACTION_TYPE_SEND_WHATSAPP }}');

                modal.modal("show");
            });

            $(".email_link").click(function(){
                var modal = $("#modal-sms-whatsapp-email-form");

                modal.find("form").find('input:text, textarea, select').val('').find('input:checkbox, input:radio').prop('checked', false);

                modal.find(".modal-title").text("Email");
                modal.find(".action_type").val('{{ LeadHistory::ACTION_TYPE_SEND_EMAIL }}');

                modal.modal("show");
            });

            $(".more-less-text").srParagraph();

            $("#purpose_price, #shipping_fee, #shipping_fee_gst_per").keyup(cal_price);
            $("#purpose_price, #shipping_fee, #shipping_fee_gst_per").on("paste", cal_price);
        });
    </script>
@endsection
