<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-bordered summary-table">
                <thead>
                    <tr class="border-bottom-primary">
                        <th><?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?></th>
                        <th>Parent</th>
                        <th><?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?></th>
                        <th><?= sortable_anchor('menu_display_order', get_value_from_list($field_labels, 'menu_display_order', 'menu_display_order')) ?></th>
                        <th><?= sortable_anchor('is_link', get_value_from_list($field_labels, 'is_link', 'is_link')) ?></th>
                        <th><?= sortable_anchor('is_active', 'Activate / De-Active') ?></th>
                        <th>Menu Info</th>
                        <th>Info</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <th scope="row">{{ $record->id }}</th>
                            <td>{{ get_value_from_list($tree_list, $record->parent_id) }}</td>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->menu_display_order }}</td>
                            <td>{{ $record->is_link ? 'Yes' : "No" }}</td>
                            <td>
                                <x-Backend.active_deactive :isActive="$record->is_active" :id="$record->id" :routePrefix="$routePrefix" />
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
                                    {{  get_value_from_list($field_labels, 'route_name', 'route_name') }} : 
                                    <span>{{ $record->route_name }}</span>
                                </label>

                                <label class="info">
                                    {{  get_value_from_list($field_labels, 'css_class', 'css_class') }} : 
                                    <span>{{ $record->css_class }}</span>
                                </label>

                                <label class="info">
                                    {{  get_value_from_list($field_labels, 'icon_css_class', 'icon_css_class') }} : 
                                    <span>{{ $record->icon_css_class }}</span>
                                </label>

                                <hr>
                                {!! array_print($record->url_params) !!}
                            </td>
                            <td>
                                <x-Backend.index-table-info :record="$record" :userList="$userListCache" />
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
