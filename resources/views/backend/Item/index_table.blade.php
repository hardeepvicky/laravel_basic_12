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
                                    $field = 'name';
                                    ?>
                                    <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm" />
                                </div>
                            </div>
                        </th>
                        <th style="width:15%">
                            <?php
                            $field = "root_item_category_id";
                            ?>
                            <x-Inputs.drop-down :name="$field" :label="$false" :value="$search[$field]" :list="$root_item_category_list" class="select2" pleaseSelect="All" class="select2 form-select-sm"/>
                        </th>
                        <th></th>
                        <th></th>
                        <th style="width:8%">
                            <?php
                            $field = "item_brand_id";
                            ?>
                            <x-Inputs.drop-down :name="$field" :label="$false" :value="$search[$field]" :list="$item_brand_list" class="select2" pleaseSelect="All" class="select2 form-select-sm"/>
                        </th>
                        <th style="width:8%">
                            <?php
                            $field = "size";
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm"/>
                        </th>
                        <th style="width:8%">
                            <?php
                            $field = "specification";
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$false" :value="$search[$field]" class="form-control-sm"/>
                        </th>
                    </tr>
                    <tr class="border-bottom-primary">
                        <th>
                            <?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?>
                            | <?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?>
                        </th>
                        <th><?= sortable_anchor('rootItemCategory.name', get_value_from_list($field_labels, 'root_item_category_id', 'root_item_category_id')) ?></th>
                        <th><?= sortable_anchor('parentItemCategory.name', get_value_from_list($field_labels, 'parent_item_category_id', 'parent_item_category_id')) ?></th>
                        <th><?= sortable_anchor('department.name', get_value_from_list($field_labels, 'dept_id', 'dept_id')) ?></th>
                        <th>{{ get_value_from_list($field_labels, 'item_brand_id', 'item_brand_id') }}</th>
                        <th>{{ get_value_from_list($field_labels, 'size', 'size') }}</th>
                        <th>{{ get_value_from_list($field_labels, 'specification', 'specification') }}</th>
                        <th style="width:10%">{{ get_value_from_list($field_labels, 'main_image_id', 'main_image_id') }}</th>
                        <th style="width:8%">
                            <?= sortable_anchor('list_display_order', get_value_from_list($field_labels, 'list_display_order', 'list_display_order')) ?>
                        </th>
                        <th style="width:8%"><?= sortable_anchor('is_active', 'Activate / De-Active') ?></th>
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
                                <x-Backend.celec-summary-table-first-column :record="$record" :routePrefix="$routePrefix"
                                    buttons="copy,edit,delete,tr_details">
                                    <div class="label">
                                        {{ $record->id . ' | ' . $record->name }}
                                    </div>
                                </x-Backend.celec-summary-table-first-column>
                            </th>
                            <td>{{ $record->rootItemCategory?->getDisplayName() }}</td>
                            <td>{{ $record->parentItemCategory?->getDisplayName() }}</td>
                            <td>{{ $record->department?->getDisplayName() }}</td>
                            <td>{{ $record->itemBrand?->getDisplayName() }}</td>
                            <td>{{ $record->size }}</td>
                            <td>{{ $record->specification }}</td>
                            <td id="main_image-{{ $record->id }}">
                                @if ($record->mainImage?->file_url)
                                    <a class="fancybox"
                                        href="{{ $record->mainImage->file_url }}">
                                        <img class="img-thumbnail small-img" src="{{ $record->mainImage->file_url }}" />
                                    </a>
                                @endif
                            </td>
                            <td class="text-center">
                                <a class="x-editable" href="javascript:void();"id="list_display_order"
                                    data-type="number" data-name="list_display_order" data-pk="{{ $record->id }}"
                                    data-url="{{ route($routePrefix . '.ajax_update_field') }}"
                                    data-title="Enter Display Order">
                                    {{ $record->list_display_order }}
                                </a>
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
                        <tr id="record-{{ $record->id }}" class="hidden">
                            <td style="background-color: var(--body--bs-body-bg)"></td>
                            <td colspan="11">
                                <div class="row">
                                    <div class="col-md-12 col-xl-5">
                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'part_no') }} :
                                            <span>
                                                {{ $record->part_no }}
                                            </span>
                                        </label>

                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'sku') }} :
                                            <span>
                                                {{ $record->sku }}
                                            </span>
                                        </label>

                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'weight') }} :
                                            <span>
                                                {{ $record->weight }} K.g.
                                            </span>
                                        </label>

                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'minimum_order_qty') }} :
                                            <span>
                                                {{ $record->minimum_order_qty }}
                                            </span>
                                        </label>

                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'origin_id') }} :
                                            <span>
                                                {{ $record->origin?->getDisplayName() }}
                                            </span>
                                        </label>

                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'certification_id') }} :
                                            <span>
                                                {{ $record->certification?->getDisplayName() }}
                                            </span>
                                        </label>

                                        <h4 class="mt-2 mb-2 b-b-info">
                                            Description
                                        </h4>

                                        <p class="mb-2 b-b-info">
                                            {!! $record->description !!}
                                        </p>

                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'tag_name', 'tag_name') }}
                                            :
                                            <span>
                                                {{ implode(", ", $record->tags->pluck("name")->toArray()) }}
                                            </span>
                                        </label>
                                    </div>

                                    <div class="col-md-12 col-xl-7">
                                        <div class="card">
                                            <div class="card-header border-l-secondary card-no-border">
                                                <h3>Images</h3>
                                            </div>
                                            <div class="card-body">

                                                <div class="file-content mt-2">
                                                    <ul class="d-flex flex-row files-content">
                                                        @foreach ($record->images as $image)
                                                            <?php
                                                            $image->afterFind();
                                                            ?>
                                                            <li class="folder-box d-flex align-items-center">
                                                                <div class="file-block card "
                                                                    data-id="{{ $image->id }}">
                                                                    <i class="fas fa-check check-icon"></i>
                                                                    <div class="card-header">
                                                                        <a href="{{ $image->file_url }}"
                                                                            class="fancybox image-container"
                                                                            data-fancybox="image">
                                                                            <img class="full-img lazy-load-image"
                                                                                src="{{ $image->file_url }}"
                                                                                alt="{{ $image->file_name }}" />
                                                                            <div class="image-loader"></div>
                                                                            <div class="error-icon" aria-hidden="true">
                                                                                &#x26A0;</div>
                                                                        </a>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <div
                                                                            class="d-flex justify-content-center align-items-center">
                                                                            <a href="{{ route('item.mark_as_main_image', ['id' => $record->id, 'document_file_id' => $image->id]) }}"
                                                                                data-main_image_id="main_image-{{ $record->id }}"
                                                                                class="btn btn-secondary btn-sm mark_as_main_image">
                                                                                Mark As Main Image
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <x-Backend.document-files :records="$record->otherFiles" />
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
