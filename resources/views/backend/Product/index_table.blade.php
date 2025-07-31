<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width:10%">
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
                        <th><?= sortable_anchor('category.name', get_value_from_list($field_labels, 'product_category_id', 'product_category_id')) ?></th>
                        <th><?= sortable_anchor('model_name', get_value_from_list($field_labels, 'model_name', 'model_name')) ?></th>
                        <th><?= sortable_anchor('short_name', get_value_from_list($field_labels, 'short_name', 'short_name')) ?></th>
                        <th>{{ get_value_from_list($field_labels, 'main_image_id', 'main_image_id') }}</th>
                        <th style="width:10%"><?= sortable_anchor('list_display_order', get_value_from_list($field_labels, 'list_display_order', 'list_display_order')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('is_active', 'Activate / De-Active') ?></th>
                        <th style="width:10%"><?= sortable_anchor('created_at', get_value_from_list($field_labels, 'created_at', 'created_at')) ?></th>
                        <th style="width:10%"><?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)                   
                        <tr>
                            <th scope="row">
                                <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix" buttons="copy,edit,delete,tr_details">
                                    <div class="label">
                                        {{ $record->id . " | " . $record->name }}
                                    </div>
                                </x-Backend.celec-summary-table-first-column>
                            </th>
                            <td>{{ get_value_from_list($product_category_list, $record->product_category_id) }}</td>
                            <td>{{ $record->model_name }}</td>
                            <td>{{ $record->short_name }}</td>
                            <td>
                                @if ($record->mainImage?->file_url)
                                <a class="fancybox" href="{{ $record->mainImage->file_url }}">
                                    <img class="img-thumbnail small-img" src="{{ $record->mainImage->file_url }}" />
                                </a>
                                @endif
                            </td>
                            <td class="text-center">
                                <a class="x-editable" href="#" id="list_display_order" data-type="number"
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
                            <td colspan="9">
                                <div class="row">
                                    <div class="col-md-12 col-xl-5">
                                        <table class="table table-sm summary-table table-bordered i-data-table">
                                            <thead>
                                                <tr>
                                                    <th style="width:5%">Sr.No.</th>
                                                    <th style="width:50%" data-search="like" data-sort="text">Custom Attrbute</th>
                                                    <th style="width:40%" data-search="like" data-sort="text">Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $a = 0; ?>
                                                @foreach($record->productAttributes as $productAttribute)
                                                <?php $a++; ?>
                                                <tr>
                                                    <td>{{ $a }}</td>
                                                    <td>{{ $productAttribute->categoryAttribute?->getDisplayName() }}</td>
                                                    <td>{{ $productAttribute->getDisplayValue() }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-md-12 col-xl-7">
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
