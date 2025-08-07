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
                            <?php
                            $field = "name";
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]"/>
                        </th>                        
                    </tr>
                    <tr class="border-bottom-primary">
                        <th style="width:15%">
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            | <?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?>
                        </th>
                        <th><?= sortable_anchor('type', get_value_from_list($field_labels, 'type', 'type')) ?></th>
                        <th style="width:12%"><?= sortable_anchor('mobile', get_value_from_list($field_labels, 'mobile', 'mobile')) ?></th>
                        <th style="width:12%"><?= sortable_anchor('mobile2', get_value_from_list($field_labels, 'mobile2', 'mobile2')) ?></th>
                        <th style="width:12%"><?= sortable_anchor('phone_no', get_value_from_list($field_labels, 'phone_no', 'phone_no')) ?></th>
                        <th>Address</th>
                        <th style="width:12%"><?= sortable_anchor('list_display_order', get_value_from_list($field_labels, 'list_display_order', 'list_display_order')) ?></th>
                        <th style="width:12%"><?= sortable_anchor('is_active', 'Activate / De-Active') ?></th>
                        <th style="width:12%"><?= sortable_anchor('created_at', get_value_from_list($field_labels, 'created_at', 'created_at')) ?></th>
                        <th style="width:12%"><?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?></th>
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
                            <td>{{ get_value_from_list($type_list, $record->type) }}</td>
                            <td>{{ $record->mobile }}</td>
                            <td>{{ $record->mobile2 }}</td>
                            <td>{{ $record->phone_no }}</td>
                            <td>{{ $record->address }}</td>
                            <td class="text-center">
                                <a class="x-editable" href="javascript:void();"id="list_display_order" data-type="number"
                                    data-name="list_display_order"
                                    data-pk="{{ $record->id }}"
                                    data-url="{{ route($routePrefix . '.ajax_update_field') }}"
                                    data-title="Enter Display Order">
                                    {{ $record->list_display_order }}
                                </a>
                            </td>
                            <td>
                                <x-Backend.active_deactive :isActive="$record->is_active" :id="$record->id" :routePrefix="$routePrefix" />
                            </td>
                            <td>
                                <x-Backend.created-and-by :datetime="$record->created_at" :userId="$record->created_by" />
                            </td>
                            <td>
                                <x-Backend.created-and-by :datetime="$record->updated_at" :userId="$record->updated_by" />
                            </td>
                        </tr>
                        <tr id="record-{{ $record->id }}" class="hidden">
                            <td style="background-color: var(--body--bs-body-bg)"></td>                            
                            <td colspan="8">
                                <div class="row">
                                    <div class="col-md-6 col-xl-3">
                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'address') }} :
                                            <span>
                                                {{ $record->address }}
                                            </span>
                                        </label>

                                        <h4 class="mt-2 mb-2 b-b-info">
                                            Description
                                        </h4>

                                        <p class="mb-2">
                                            {!! $record->description !!}
                                        </p>
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card">
                                            <div class="card-header border-l-secondary card-no-border">
                                                <h3>Terms</h3>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm summary-table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>{{ get_value_from_list($term_field_labels, 'name') }}</th>
                                                            <th>{{ get_value_from_list($term_field_labels, 'is_active') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($record->terms as $i => $term)
                                                        <tr>
                                                            <td>{{ $i + 1 }}</td>
                                                            <td>{{ $term->name }}</td>
                                                            <td>{{ $term->is_active ? "Active" : "No" }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>     
                                    </div>
                                    <div class="col-md-6 col-xl-5">
                                        <x-Backend.document-files :records="$record->documentFile" />
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
