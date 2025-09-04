<x-Backend.pagination-links :records="$records" />
<div class="table-responsive">
    <table class="table table-bordered summary_table_with_search">
        <thead>
            <tr>
                <th style="width:12%">
                    <div class="row">
                        <div class="col-sm-4">
                            <span class="btn btn-light btn-sm text-dark border-primary clear_search_conditions mb-1">Clear</span>
                        </div>
                        <div class="col-sm-8">
                            <?php
                            $field = 'name';
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm" />
                        </div>
                    </div>
                </th>
                <th style="width:8%">
                    <?php
                    $field = 'type';
                    ?>
                    <x-Inputs.drop-down :name="$field" :label="$false" :value="$search[$field]" :list="$type_list"
                        pleaseSelect="All"
                        class="select2 form-select-sm" />
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
                <th style="width:8%">
                    <?php
                    $field = 'mobile';
                    ?>
                    <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm" />
                </th>
                <th style="width:8%">
                    <?php
                    $field = 'mobile2';
                    ?>
                    <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm" />
                </th>
                <th style="width:10%">
                    <?php
                    $field = 'email';
                    ?>
                    <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm" />
                </th>
            </tr>
            <tr class="border-bottom-primary">
                <th>Actions</th>
                <th><?= sortable_anchor('type', 'Type') ?></th>
                <th>Location</th>
                <th><?= sortable_anchor('mobile', 'Mobile') ?></th>
                <th><?= sortable_anchor('mobile2', 'Mobile2') ?></th>
                <th><?= sortable_anchor('email', 'Email') ?></th>
                <th><?= sortable_anchor('status_type', 'Status') ?></th>
                <th><?= sortable_anchor('action_type', 'Action Type') ?></th>
                <th><?= sortable_anchor('response_type_id', 'Response Type') ?></th>
                <th><?= sortable_anchor('remarks', 'Remarks') ?></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $record)
                <tr>
                    <td>
                        <div class="celec_info">
                            <div class="label">
                                {!! $record->name !!}
                            </div>

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
                    <td>
                        {{ $record->state->getShortName() }} >> {{ $record->district_name }} >> {{ $record->city_name }}
                    </td>
                    <td>{{ $record->mobile }}</td>
                    <td>{{ $record->mobile2 }}</td>
                    <td>{{ $record->email }}</td>
                    <td>{{ $record->status_type }}</td>
                    <td>{{ $record->action_type }}</td>
                    <td>{{ get_value_from_list($response_type_list, $record->response_type_id) }}</td>
                    <td>{{ $record->remarks }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<x-Backend.pagination-links :records="$records" />