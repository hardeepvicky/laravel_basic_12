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
                            <span class="btn btn-light btn-sm text-dark border-primary clear_search_conditions mb-1">Clear</span>
                        </th> 
                        <th>
                            <?php
                            $field = "sale_order_no";
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                                :value="$search[$field]" class="form-control-sm"/>
                        </th>        
                        <th>
                            <?php
                            $field = "product_id";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :value="$search[$field]" 
                                :list="$product_list" :value="$search[$field]" pleaseSelect="All" class="select2 form-select-sm" />
                        </th>
                        <th>
                            <?php
                            $field = "make_id";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" 
                                :value="$search[$field]" :list="$make_list" :value="$search[$field]" pleaseSelect="All" class="select2 form-select-sm" />
                        </th>                 
                        <th>
                            <?php
                            $field = "assign_to_user_id";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" 
                                :value="$search[$field]" :list="$user_list" :value="$search[$field]" pleaseSelect="All" class="select2 form-select-sm" />
                        </th>
                        <th></th>                 
                        <th></th>  
                        <th>
                            <?php
                            $field = "status";
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" 
                                :value="$search[$field]" :list="$status_list" :value="$search[$field]" pleaseSelect="All" class="select2 form-select-sm" />
                        </th>      
                        <th style="width:8%">
                            <?php
                            $field = 'from_order_date_time';
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm date-picker" />
                            <?php
                            $field = 'to_order_date_time';
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm date-picker" />
                        </th>
                        <th style="width:8%">
                            <?php
                            $field = 'from_manufacture_complete_date_time';
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm date-picker" />
                            <?php
                            $field = 'to_manufacture_complete_date_time';
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm date-picker" />
                        </th>         
                    </tr>
                    <tr class="border-bottom-primary">
                        <th style="width:6%">
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                        </th>
                        <th style="width:10%">
                            Sale Order No.
                        </th>
                        <th style="width:10%">
                            <?= sortable_anchor('product.model_name', get_value_from_list($field_labels, 'product_id', 'product_id')) ?>
                        </th>
                        <th style="width:10%">
                            <?= sortable_anchor('make.name', get_value_from_list($field_labels, 'make_id', 'make_id')) ?>
                        </th>
                        <th style="width:15%">                            
                            <?= sortable_anchor('user.person_name', get_value_from_list($field_labels, 'assign_to_user_id', 'assign_to_user_id')) ?>
                        </th>
                        <th style="width:8%"><?= sortable_anchor('order_qty', get_value_from_list($field_labels, 'order_qty', 'order_qty')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('manufactured_qty', get_value_from_list($field_labels, 'manufactured_qty', 'manufactured_qty')) ?></th>
                        <th style="width:8%"><?= sortable_anchor('status', get_value_from_list($field_labels, 'status', 'status')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('order_date_time', get_value_from_list($field_labels, 'order_date_time', 'order_date_time')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('manufacture_complete_date_time', get_value_from_list($field_labels, 'manufacture_complete_date_time', 'manufacture_complete_date_time')) ?></th>
                        <th style="width:10%"><?= get_value_from_list($field_labels, 'manufacture_complete_by', 'manufacture_complete_by') ?></th>
                        <th style="width:10%"><?= get_value_from_list($field_labels, 'remarks', 'remarks') ?></th>                        
                        <th style="width:10%"><?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('updated_by', get_value_from_list($field_labels, 'updated_by', 'updated_by')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <th scope="row">
                                <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" buttons="edit">
                                    <div class="label">
                                        {{ $record->id }}
                                    </div>
                                </x-Backend.celec-summary-table-first-column>
                            </th>
                            <td>
                                <a href="{{ route('sale-order.index', ['id' => $record->saleOrderProduct?->sale_order_id]) }}" target="_blank">
                                    {{ $record->saleOrderProduct?->saleOrder->getDisplayName() }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('product.index', ['id' => $record->product_id]) }}" target="_blank">
                                    {{ $record->product->getDisplayName() }}
                                </a>
                            </td>
                            <td>{{ $record->make->getDisplayName() }}</td>
                            <td>{{ get_value_from_list($userListCache, $record->assign_to_user_id) }}</td>
                            
                            <td>{{ $record->order_qty }}</td>
                            <td>{{ $record->manufactured_qty }}</td>
                            <td>{{ get_value_from_list($status_list, $record->status) }}</td>
                            <td>
                                <x-Backend.celec-date-time :value="$record->order_date_time"/>
                            </td>
                            <td>
                                <x-Backend.celec-date-time :value="$record->manufacture_complete_date_time"/>
                            </td>
                            <td>{{ get_value_from_list($userListCache, $record->manufacture_complete_by) }}</td>
                            <td>{{ $record->remarks }}</td>
                            <td>
                                <x-Backend.celec-date-time :value="$record->updated_at"/>
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
