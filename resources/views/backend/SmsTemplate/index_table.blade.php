<?php

use App\Models\Task;
$false = false;
?>
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
                            $field = "template_name";
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm"/>
                        </th>    
                        <th>
                            <?php
                            $field = "language";
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm"/>
                        </th>    
                    </tr>
                    <tr class="border-bottom-primary">
                        <th style="width:10%">
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            | <?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?>
                        </th>
                        <th style="width:10%"><?= sortable_anchor('template_name', get_value_from_list($field_labels, 'template_name', 'template_name')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('language', 'Language') ?></th>
                        <th>Template</th>
                        <th>Placeholders</th>
                        <th style="width:8%"><?= sortable_anchor('is_active', 'Active / De-Active') ?></th>
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
                            <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" buttons="edit,copy,delete" >
                                <div class="label">
                                    {{ $record->id . " | " . $record->name }}
                                </div>
                            </x-Backend.celec-summary-table-first-column>
                        </th>
                        <td>{{ $record->template_name }}</td>
                        <td>{{ $record->language }}</td>
                        <td>{{ $record->template }}</td>
                        <td>
                            {!! array_print($record->placeholders) !!}
                        </td>
                        <td>
                            <x-Backend.active_deactive :isActive="$record->is_active" :id="$record->id" :routePrefix="$routePrefix" />
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <x-Backend.pagination-links :records="$records" />
    </div>
</div>