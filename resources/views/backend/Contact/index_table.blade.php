<x-Backend.pagination-links :records="$records" />
<div class="table-responsive">
    <table class="table table-bordered summary_table_with_search">
        <thead>
            <tr>
                <th style="width:6%">
                    <span class="btn btn-light btn-sm text-dark border-primary clear_search_conditions mb-1">Clear</span>
                </th>
                <th style="width:12%">                        
                    <?php
                    $field = 'type';
                    ?>
                    <x-Inputs.drop-down :name="$field" :label="$false" :value="$search[$field]" :list="$type_list"
                        pleaseSelect="All"
                        class="select2 form-select-sm" />
                </th>
                <th style="width:10%">
                    <?php
                    $field = 'name';
                    ?>
                    <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm" />
                </th>
                <th>
                    <?php
                    $field = 'state_id';
                    ?>
                    <x-Inputs.drop-down :name="$field" :label="$false" :value="$search[$field]" :list="$state_list"
                        pleaseSelect="All"
                        class="modal-select2 form-select-sm" />
                </th>
                <th>
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
                <th><?= sortable_anchor('name', 'Name') ?></th>
                <th><?= sortable_anchor('state_name', 'State') ?></th>                
                <th><?= sortable_anchor('city_name', 'City') ?></th>                
                <th><?= sortable_anchor('mobile', 'Mobile') ?></th>
                <th><?= sortable_anchor('mobile2', 'Mobile2') ?></th>
                <th><?= sortable_anchor('email', 'Email') ?></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $record)
                <tr>
                    <td>
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
                    </td>
                    <td>{{ get_value_from_list($type_list, $record->type) }}</td>
                    <td>{{ $record->name }}</td>
                    <td>{{ $record->state_name }}</td>
                    <td>
                        {{ $record->district_name }} >> {{ $record->city_name }}
                    </td>
                    <td>{{ $record->mobile }}</td>
                    <td>{{ $record->mobile2 }}</td>
                    <td>{{ $record->email }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<x-Backend.pagination-links :records="$records" />