<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-bordered summary-table summary_table_with_search">
                <thead>
                    <tr>
                        <th style="width:15%">
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
                        <th>
                            <?php
                            $field = "short_name";
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm"/>
                        </th>                        
                        <th>
                            <?php
                            $field = "phonecode";
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm"/>
                        </th>                        
                        <th>
                            <?php
                            $field = "timezone";
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm"/>
                        </th>                        
                        <th>
                            <?php
                            $field = "iso3";
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm"/>
                        </th>                        
                    </tr>
                    <tr class="border-bottom-primary">
                        <th>
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            | <?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?>
                        </th>
                        <th style="width:8%"><?= sortable_anchor('short_name', get_value_from_list($field_labels, 'short_name', 'short_name')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('phonecode', get_value_from_list($field_labels, 'phonecode', 'phonecode')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('timezone', get_value_from_list($field_labels, 'timezone', 'timezone')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('iso3', get_value_from_list($field_labels, 'iso3', 'iso3')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('currency_name', get_value_from_list($field_labels, 'currency_name', 'currency_name')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('created_at', get_value_from_list($field_labels, 'created_at', 'created_at')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('created_by', get_value_from_list($field_labels, 'created_by', 'created_by')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('updated_by', get_value_from_list($field_labels, 'updated_by', 'updated_by')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                    <?php
                        $first_col_info = [
                            $record->id . " | " . $record->name
                        ];
                    ?>
                        <tr>
                            <th scope="row">
                                <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" buttons="edit,delete" :labelInfo="$first_col_info"/>
                            </th>
                            <td>{{ $record->short_name }}</td>
                            <td>{{ $record->phonecode }}</td>
                            <td>{{ $record->timezone }}</td>
                            <td>{{ $record->iso3 }}</td>
                            <td>{{ $record->currency_name }}</td>
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <x-Backend.pagination-links :records="$records" />
    </div>
</div>
