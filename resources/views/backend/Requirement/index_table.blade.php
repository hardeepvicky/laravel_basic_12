<?php
use App\Models\Requirement;
$formatter = new NumberFormatter('en_IN', NumberFormatter::CURRENCY);
$formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);
?>
<div class="card" id="page-summary">
    <div class="card-header">
        <a href="{{ route($routePrefix . '.export.csv') }}" class="btn btn-secondary">Export CSV</a>
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-bordered summary_table_with_search">
                <thead>
                    <tr>
                        <th style="width:10%">
                            <div class="row">
                                <div class="col-sm-4">
                                    <span class="btn btn-light btn-sm text-dark border-primary clear_search_conditions mb-1">Clear</span>
                                </div>
                                <div class="col-sm-8">
                                    <?php
                                    $field = 'order_no';
                                    ?>
                                    <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm" />
                                </div>
                            </div>
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'branch_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" 
                                :list="$branch_list"
                                class="select2 form-select-sm"
                            />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'dept_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" 
                                :list="$dept_list"
                                class="select2 form-select-sm"
                            />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'sub_dept_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" 
                                :list="$sub_dept_list"
                                class="select2 form-select-sm"
                            />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'item_id';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" 
                                :list="$item_list"
                                class="select2 form-select-sm"
                            />
                        </th>
                        <th style="width:10%">
                            <?php
                            $field = 'part_no';
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm" />
                        </th>
                        <th></th>
                        <th style="width:10%">
                            <?php
                            $field = 'status';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" 
                                :list="$status_list"
                                class="select2 form-select-sm"
                            />
                        </th>
                    </tr>
                    <tr class="border-bottom-primary">
                        <th>
                            <label class="form-check custom-checkbox-group">
                                <input type="checkbox" class="form-check-input chk-select-all" data-sr-chkselect-children="input.requirement">
                            </label>
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            |
                            <?= sortable_anchor('order_no', get_value_from_list($field_labels, 'order_no', 'order_no')) ?>
                        </th>
                        <th><?= sortable_anchor('branch.name', get_value_from_list($field_labels, 'branch_id', 'branch_id')) ?></th>
                        <th><?= sortable_anchor('department.name', get_value_from_list($field_labels, 'dept_id', 'dept_id')) ?></th>
                        <th><?= sortable_anchor('subDepartment.name', get_value_from_list($field_labels, 'sub_dept_id', 'sub_dept_id')) ?></th>
                        <th><?= sortable_anchor('item.name', get_value_from_list($field_labels, 'item_id', 'item_id')) ?></th>
                        <th><?= sortable_anchor('part_no', get_value_from_list($field_labels, 'part_no', 'part_no')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('qty', get_value_from_list($field_labels, 'qty', 'qty')) ?></th>
                        <th><?= sortable_anchor('status', get_value_from_list($field_labels, 'status', 'status')) ?></th>
                        <th style="width:8%">
                            <?= get_value_from_list($field_labels, 'remarks', 'remarks') ?>
                        </th>                        
                        <th style="width:8%">
                            <?= sortable_anchor('created_at', get_value_from_list($field_labels, 'created_at', 'created_at')) ?>
                        </th>
                        <th style="width:8%">
                            <?= sortable_anchor('created_by', get_value_from_list($field_labels, 'created_by', 'created_by')) ?>
                        </th>
                        <th style="width:8%">
                            <?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?>
                        </th>
                        <th style="width:8%">
                            <?= sortable_anchor('updated_by', get_value_from_list($field_labels, 'updated_by', 'updated_by')) ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <th scope="row">
                                <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" buttons="edit,copy,delete">
                                    <div class="label">
                                        <label class="form-check custom-checkbox-group">
                                            @if($record->status == Requirement::STATUS_PENDING)
                                                <input type="checkbox" class="form-check-input requirement" value={{ $record->id }}>
                                            @endif

                                            {{ $record->order_no }}
                                        </label>
                                    </div>
                                </x-Backend.celec-summary-table-first-column>
                            </th>
                            <td>
                                {{ $record->branch?->getDisplayName() }}
                            </td>
                            <td>
                                {{ $record->department?->getDisplayName() }}
                            </td>
                            <td>
                                {{ $record->subDepartment?->getDisplayName() }}
                            </td>
                            <td>
                                {{ $record->item?->getDisplayName() }}
                            </td>
                            <td>
                                {{ $record->part_no }}
                            </td>
                            <td>
                                {{ $record->qty }} {{ $record->uom?->getDisplayName() }}
                            </td>
                            <td>
                                {{ get_value_from_list($status_list, $record->status) }}
                            </td>
                            <td>
                                {{ $record->remarks }}
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
                <tfoot>
                    <tr>
                        <td colspan="2">
                            <span id="create_purchase_order" class="btn btn-secondary btn-sm mb-1">Create Purchase Order</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <x-Backend.pagination-links :records="$records" />
    </div>
</div>


<script>
    $(function(){
        $("#create_purchase_order").click(function(){
            var id_list = [];

            $("input.requirement:checked").each(function(){
                var v = $(this).val();

                id_list.push(v);
            });

            if (id_list.length <= 0)
            {
                $.events.onUserError("Please Select at least one Requirement");
                return false;
            }

            var params = [
                {
                    name : "_token",
                    value : '{{ csrf_token() }}'
                },
                {
                    name : "_method",
                    value : 'POST'
                },
            ];

            for(var i in id_list)
            {
                params.push({
                    name : "requirement_ids[]",
                    value : id_list[i]
                });
            }

            add_form_to_body_and_submit("post", "/purchase-order/create_from_requirements", params);
        });
    });
</script>