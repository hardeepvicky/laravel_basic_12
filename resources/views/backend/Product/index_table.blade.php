<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr class="border-bottom-primary">
                        <th><?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?></th>                        
                        <th><?= sortable_anchor('category.name', get_value_from_list($field_labels, 'product_category_id', 'product_category_id')) ?></th>
                        <th><?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?></th>
                        <th><?= sortable_anchor('model_name', get_value_from_list($field_labels, 'model_name', 'model_name')) ?></th>
                        <th><?= sortable_anchor('short_name', get_value_from_list($field_labels, 'short_name', 'short_name')) ?></th>
                        <th><?= sortable_anchor('list_display_order', get_value_from_list($field_labels, 'list_display_order', 'list_display_order')) ?></th>
                        <th><?= sortable_anchor('is_active', 'Activate / De-Active') ?></th>
                        <th style="width:6%"><?= sortable_anchor('created_at', get_value_from_list($field_labels, 'created_at', 'created_at')) ?></th>
                        <th style="width:6%"><?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <th scope="row">{{ $record->id }}</th>
                            <td>{{ get_value_from_list($product_category_list, $record->product_category_id) }}</td>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->model_name }}</td>
                            <td>{{ $record->short_name }}</td>
                            <td>{{ $record->list_display_order }}</td>
                            <td>
                                <x-Backend.active_deactive :isActive="$record->is_active" :id="$record->id" :routePrefix="$routePrefix" />
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
