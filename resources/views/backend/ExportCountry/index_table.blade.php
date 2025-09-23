<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-bordered summary_table_with_search">
                <thead>
                    <tr>
                        <th>
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
                                $field = 'iso3';
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm" />
                        </th>
                        <th>
                            <?php
                                $field = 'numeric_code';
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm" />
                        </th>
                        <th>
                            <?php
                                $field = 'iso2';
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm" />
                        </th>
                        <th>
                            <?php
                                $field = 'phonecode';
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm" />
                        </th>
                    </tr>
                    <tr class="border-bottom-primary">
                        <th style="width:15%">
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            | <?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?>
                        </th>
                        <th style="width:10%"><?= sortable_anchor('iso3', get_value_from_list($field_labels, 'iso3', 'iso3')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('numeric_code', get_value_from_list($field_labels, 'numeric_code', 'numeric_code')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('iso2', get_value_from_list($field_labels, 'iso2', 'iso2')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('phonecode', get_value_from_list($field_labels, 'phonecode', 'phonecode')) ?></th>
                        <th style="width:10%"><?= get_value_from_list($field_labels, 'emoji', 'emoji') ?></th>
                        <th style="width:10%"><?= sortable_anchor('created_at', get_value_from_list($field_labels, 'created_at', 'created_at')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <th scope="row">                               
                                <div class="celec_info">
                                    <label class="form-check custom-checkbox-group">
                                        <input type="checkbox" class="form-check-input contact" data-type="{{ $record->type }}" value={{ $record->id }}>
                                        <span>{{ $record->name }}</span>
                                    </label>

                                    <div class="divider"></div>
                                    <div class="p-1">
                                        <ul class="action">
                                            <?php
                                            $route_prefix_2 = "export-" . $record->type;
                                            ?>
                                            @if (in_array($record->type, ["lead", "customer", "party", "dealer"]))
                                            <li>Party
                                                <a class="btn btn-outline-info summary-action-button" href="{{ route($route_prefix_2 . '.history',[$record->ref_id]) }}">
                                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                                </a>
                                            </li>
                                            @endif
                                            <li>
                                                <a class="btn btn-outline-primary summary-action-button" href="{{ route($route_prefix_2 . '.edit',[$record->ref_id]) }}">
                                                    <i class="icon-pencil-alt"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <x-Backend.summary-delete-button url="{{ route($route_prefix_2 . '.destroy', [$record->ref_id]) }}" />
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </th>
                            <td>{{ $record->iso3 }}</td>
                            <td>{{ $record->numeric_code }}</td>
                            <td>{{ $record->iso2 }}</td>
                            <td>{{ $record->phonecode }}</td>
                            <td>{{ $record->emoji }}</td>
                            <td>
                                <x-Backend.celec-date-time :value="$record->created_at" />
                            </td>
                            <td>
                                <x-Backend.celec-date-time :value="$record->updated_at" :userId="$record->updated_by" />
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
