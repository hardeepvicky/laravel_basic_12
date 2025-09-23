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
                    </tr>
                    <tr class="border-bottom-primary">
                        <th style="width:25%">
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            | <?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?>
                        </th>
                        <th style="width:8%"><?= sortable_anchor('for_all_branches', get_value_from_list($field_labels, 'for_all_branches', 'for_all_branches')) ?></th>
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
                                <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" buttons="edit,copy,delete,tr_details">
                                    <div class="label">
                                        {{ $record->id . " | " . $record->name }}
                                    </div>
                                </x-Backend.celec-summary-table-first-column>
                            </th>
                            <td>
                                <x-Backend.yes-no-label :value="$record->for_all_branches"/>
                            </td>
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
                            <td colspan="4">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="card">
                                            <div class="card-header border-l-secondary card-no-border">
                                                <h3>Dates</h3>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm summary-table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($record->holidayDate as $i => $holidayDate)
                                                        <tr>
                                                            <td>{{ $i + 1 }}</td>
                                                            <td>{{ if_date($holidayDate->date) }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-sm-6">
                                        <div class="card">
                                            <div class="card-header border-l-secondary card-no-border">
                                                <h3>Branches</h3>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm summary-table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($record->branch as $i => $branch)
                                                        <tr>
                                                            <td>{{ $i + 1 }}</td>
                                                            <td>{{ $branch->getDisplayName() }}</td>
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
