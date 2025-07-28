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
                        <th><?= sortable_anchor('name', get_value_from_list($field_labels, 'name', 'name')) ?></th>
                        <th><?= sortable_anchor('language', get_value_from_list($field_labels, 'language', 'language')) ?></th>
                        <th><?= sortable_anchor('is_active', get_value_from_list($field_labels, 'is_active', 'is_active')) ?></th>
                        <th style="width:6%"><?= sortable_anchor('created_at', get_value_from_list($field_labels, 'created_at', 'created_at')) ?></th>
                        <th style="width:6%"><?= sortable_anchor('updated_at', get_value_from_list($field_labels, 'updated_at', 'updated_at')) ?></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <th scope="row">{{ $record->id }}</th>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->language }}</td>
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

                                <span class="btn btn-info btn-sm css-toggler mt-1 mb-1"
                                    data-sr-css-class-toggle-target="#record-{{ $record->id }}"
                                    data-sr-css-class-toggle-class="hidden" data-sr-css-class-toggle-applied-text="Show"
                                    data-sr-css-class-toggle-not-applied-text="Hide">
                                    Show
                                </span>
                            </td>
                        </tr>
                        <tr id="record-{{ $record->id }}" class="hidden">
                            <td style="background-color: var(--body--bs-body-bg)"></td>
                            <td colspan="5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'default_header_values') }} :
                                            <span>
                                                {!! array_print($record->default_header_values) !!}
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'default_body_values') }} :
                                            <span>
                                                {!! array_print($record->default_body_values) !!}
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="info">
                                            {{ get_value_from_list($field_labels, 'default_button_values') }} :
                                            <span>
                                                {!! array_print($record->default_button_values) !!}
                                            </span>
                                        </label>
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
