<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-bordered summary-table">
                <thead>
                    <tr class="border-bottom-primary">
                        <th><?= sortable_anchor('id', 'ID') ?></th>
                        <th>Parent</th>
                        <th><?= sortable_anchor('name', 'Name') ?></th>
                        <th><?= sortable_anchor('menu_display_order', 'Display Order') ?></th>
                        <th><?= sortable_anchor('is_link', 'Link') ?>
                        </th>
                        <th style="width:12%"><?= sortable_anchor('is_active', 'Activate / De-Active') ?></th>
                        <th>Menu Info</th>
                        <th style="width:10%"><?= sortable_anchor('created_at', 'Created') ?></th>
                        <th style="width:10%"><?= sortable_anchor('updated_at', 'Updated') ?></th>
                        <th style="width:8%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                    <tr>
                        <th scope="row">{{ $record->id }}</th>
                        <td>{{ get_value_from_list($tree_list, $record->parent_id) }}</td>
                        <td>{{ $record->name }}</td>
                        <td>
                            <a class="x-editable" href="javascript:void();"id="menu_display_order" 
                                data-type="number" 
                                data-name="menu_display_order"
                                data-pk="{{ $record->id }}" 
                                data-url="{{ route($routePrefix . ".ajax_update_field") }}" 
                                data-title="Enter Display Order"
                            >
                                {{ $record->menu_display_order }}
                            </a>
                        </td>
                        <td>{{ $record->is_link ? 'Yes' : "No" }}</td>
                        <td>
                            <x-Backend.active_deactive :isActive="$record->is_active" :id="$record->id"
                                :routePrefix="$routePrefix" />
                        </td>
                        <td>
                            <label class="info">
                                Direct Child Count :
                                <span>{{ $record->direct_child_count }}</span>
                            </label>

                            <label class="info">
                                Nested Child Count :
                                <span>{{ $record->nested_child_count }}</span>
                            </label>

                            <label class="info">
                                Route Name :
                                <span>{{ $record->route_name }}</span>
                            </label>

                            <label class="info">
                                Css Class :
                                <span>{{ $record->css_class }}</span>
                            </label>

                            <label class="info">
                                Icon css class :
                                <span>{{ $record->icon_css_class }}</span>
                            </label>

                            <hr>
                            {!! array_print($record->url_params) !!}
                        </td>
                        <td>
                            <x-Backend.created-and-by :datetime="$record->created_at" :userId="$record->created_by" />
                        </td>
                        <td>
                            <x-Backend.created-and-by :datetime="$record->updated_at" :userId="$record->updated_by" />
                        </td>
                        <td>
                            <x-Backend.summary-comman-actions :id="$record->id" :routePrefix="$routePrefix" buttons="edit,copy,delete" />
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