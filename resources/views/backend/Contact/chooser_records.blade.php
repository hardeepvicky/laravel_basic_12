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
                            $field = 'type';
                            ?>
                            <x-Inputs.drop-down :name="$field" :label="$false" :value="$search[$field]" :list="$type_list"
                                pleaseSelect="All"
                                class="select2 form-select-sm" />
                        </div>
                    </div>
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
                <th><?= sortable_anchor('type', 'Type') ?></th>
                <th>
                    @if($multiple)
                    <label class="form-check custom-checkbox-group">
                        <input type="checkbox" class="form-check-input chk-select-all" data-sr-chkselect-children="input.contact">
                    </label>
                    @endif
                    <?= sortable_anchor('name', 'Name') ?>
                </th>
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
                    <td>{{ get_value_from_list($type_list, $record->type) }}</td>
                    <td>
                        <label class="form-check custom-checkbox-group">
                            <input type="checkbox" class="form-check-input contact" data-type="{{ $record->type }}" value={{ $record->id }}>
                            <span>{{ $record->name }}</span>
                        </label>
                    </td>
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