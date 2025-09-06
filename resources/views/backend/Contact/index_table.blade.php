<x-Backend.pagination-links :records="$records" />
<div class="table-responsive">
    <table class="table table-bordered summary_table_with_search">
        <thead>
            <tr>
                <th style="width:12%">
                    <span class="btn btn-light btn-sm text-dark border-primary clear_search_conditions mb-1">Clear</span>
                    <?php
                    $field = 'name';
                    ?>
                    <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm" />
                </th>
                <th style="width:8%">
                    <?php
                    $field = 'type';
                    ?>
                    <x-Inputs.drop-down :name="$field" :label="$false" :value="$search[$field]" :list="$type_list"
                        pleaseSelect="All"
                        class="select2 form-select-sm" />
                </th>
                <th style="width:8%">
                    <?php
                    $field = 'from_last_followup_date_time';
                    ?>
                    <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm date-picker" />
                    <?php
                    $field = 'to_last_followup_date_time';
                    ?>
                    <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm date-picker" />
                </th>
                <th style="width:8%">
                    <?php
                    $field = 'from_next_followup_date_time';
                    ?>
                    <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm date-picker" />
                    <?php
                    $field = 'to_next_followup_date_time';
                    ?>
                    <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm date-picker" />
                </th>
                <th style="width:8%">
                    <?php
                    $field = 'mobile';
                    ?>
                    <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm" />
                </th>
                <th style="width:15%">
                    <?php
                    $field = 'state_id';
                    ?>
                    <x-Inputs.drop-down :name="$field" :label="$false" :value="$search[$field]" :list="$state_list"
                        pleaseSelect="All"
                        class="modal-select2 form-select-sm mb-1" />

                    <?php
                    $field = 'city_id';
                    ?>
                    <x-Inputs.drop-down :name="$field" :label="$false" :value="$search[$field]" :list="$district_city_list"
                        pleaseSelect="All"
                        class="modal-select2 form-select-sm" />
                </th>
                <th style="width:10%">
                    <?php
                    $field = 'product_id';
                    ?>
                    <x-Inputs.drop-down :name="$field" :label="$false" :value="$search[$field]" :list="$product_list" pleaseSelect="All" class="select2 form-select-sm" />
                </th>
            </tr>
            <tr class="border-bottom-primary">
                <th>
                    @if(isset($multiple) && $multiple)
                    <label class="form-check custom-checkbox-group">
                        <input type="checkbox" class="form-check-input chk-select-all" data-sr-chkselect-children="input.contact">
                    </label>
                    @endif
                    <?= sortable_anchor('name', 'Name') ?>
                </th>
                <th><?= sortable_anchor('type', 'Type') ?></th>
                <th><?= sortable_anchor('last_followup_date_time', 'Last') ?></th>
                <th><?= sortable_anchor('next_followup_date_time', 'Next') ?></th>
                <th><?= sortable_anchor('mobile', 'Mobile') ?></th>
                <th>Location</th>
                <th>Model</th>
                <th><?= sortable_anchor('status_type', 'Status') ?></th>
                <th><?= sortable_anchor('action_type', 'Action') ?></th>
                <th><?= sortable_anchor('response_type_id', 'Response') ?></th>
                <th><?= sortable_anchor('remarks', 'Remarks') ?></th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $record)
                <tr>
                    <td>
                        <div class="celec_info">
                            <label class="form-check custom-checkbox-group">
                                <input type="checkbox" class="form-check-input contact" data-type="{{ $record->type }}" value={{ $record->id }}>
                                <span>{{ $record->name }}</span>
                            </label>

                            <div class="divider"></div>
                            <div class="p-1">
                                <ul class="action">
                                    <?php
                                    $route_prefix_2 = $record->type;
                                    ?>
                                    @if (in_array($record->type, ["lead", "customer"]))
                                    <li>
                                        <a class="btn btn-outline-info summary-action-button" href="{{ route($route_prefix_2 . '.history',[$record->id]) }}">
                                            <i class="fa-solid fa-clock-rotate-left"></i>
                                        </a>
                                    </li>
                                    @endif
                                    <li>
                                        <a class="btn btn-outline-primary summary-action-button" href="{{ route($route_prefix_2 . '.edit',[$record->id]) }}">
                                            <i class="icon-pencil-alt"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <x-Backend.summary-delete-button url="{{ route($route_prefix_2 . '.destroy', [$record->id]) }}" />
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </td>
                    <td>{{ get_value_from_list($type_list, $record->type) }}</td>
                    <td><x-Backend.celec-date-time :value="$record->last_followup_date_time"/></td>
                    <td><x-Backend.celec-date-time :value="$record->next_followup_date_time"/></td>
                    <td>{{ $record->mobile }}</td>
                    <td>
                        {{ $record->state->getShortName() }} >> {{ $record->district_name }} >> {{ $record->city_name }}
                    </td>
                    <td>
                        <?php
                        $product_list = [];
                        foreach($record->product as $product)
                        {
                            $product_list[] = $product->model_name;
                        }
                        ?>

                        @if($product_list)
                            @if (count($product_list) == 1)
                                {{ $product_list[0] }}
                            @else
                                <button class="btn btn-outline-info mb-0 me-0" type="button" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ implode(', ', $product_list) }}">
                                    {{ count($product_list) }} Products
                                </button>
                            @endif
                        @endif               
                    </td>
                    <td>{{ get_value_from_list($status_type_list, $record->status_type) }}</td>
                    <td>{{ get_value_from_list($action_type_list, $record->action_type) }}</td>
                    <td>{{ get_value_from_list($response_type_list, $record->response_type_id) }}</td>
                    <td>{{ $record->remarks }}</td>
                    
                    <td>{{ get_value_from_list($userListCache, $record->created_by) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<x-Backend.pagination-links :records="$records" />