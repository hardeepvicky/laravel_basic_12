<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr class="border-bottom-primary">
                        <th style="width:10%">
                            <?= sortable_anchor('model_class', get_value_from_list($field_labels, 'model_class', 'model_class')) ?>
                            | 
                            <?= sortable_anchor('model_id', get_value_from_list($field_labels, 'model_id', 'model_id')) ?>
                        </th>
                        <th style="width:6%"><?= sortable_anchor('action', get_value_from_list($field_labels, 'action', 'action')) ?></th>                        
                        <th style="width:10%">Change Counters</th>
                        <th style="width:60%">Changes</th>
                        <th style="width:10%"><?= sortable_anchor('created_at', get_value_from_list($field_labels, 'created_at', 'created_at')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <th scope="row">
                                {{ str_class_name_to_human_text( $record->model_class ) }} 
                                | #{{ $record->model_id }}

                                @if($record->model_display_name)
                                | {{ $record->model_display_name }}
                                @endif
                            </th>
                            <td>{{ $record->action }}</td>
                            <td>
                                @if (isset($record->changes) && is_array($record->changes))
                                <div class="h-200">
                                    {!! array_print($record->change_counters, 100) !!}
                                </div>
                                @endif
                            </td>
                            <td>
                                @if (isset($record->changes) && is_array($record->changes))
                                <div id="change-{{ $record->id }}" class="h-200">
                                    <table class="table table-sm summary-table table-bordered i-data-table">
                                        <thead>
                                            <tr>
                                                <th style="width:5%">Sr.No.</th>
                                                <th style="width:5%" data-sort="text">Sub Action</th>
                                                <th style="width:20%" data-search="like" data-sort="text">Field</th>
                                                <th style="width:35%" data-search="like" data-sort="text">Previous Value</th>
                                                <th style="width:35%" data-search="like" data-sort="text">New Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $a = 0; ?>
                                            @foreach($record->changes as $field => $arr)
                                            <?php $a++; ?>
                                            <tr>
                                                <td>{{ $a }}</td>
                                                <td>{{ $arr['type'] ?? "" }}</td>
                                                <td>{{ $arr['label'] ?? "" }}</td>
                                                <td>{{ $arr['previous'] ?? "" }}</td>
                                                <td>{{ $arr['current'] ?? "" }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <span class="btn btn-info btn-sm css-toggler mt-1 mb-1"
                                    data-sr-css-class-toggle-target="#change-{{ $record->id }}"
                                    data-sr-css-class-toggle-class="h-200" data-sr-css-class-toggle-applied-text="More"
                                    data-sr-css-class-toggle-not-applied-text="Less">
                                    More
                                </span>
                                @endif
                            </td>
                            <td>
                                <x-Backend.created-and-by :datetime="$record->created_at_user_tz" :userId="$record->created_by" />
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
