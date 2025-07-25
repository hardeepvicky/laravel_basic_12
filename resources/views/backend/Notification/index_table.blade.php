<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr class="border-bottom-primary">
                        <th style="width:6%"><?= sortable_anchor('id', get_value_from_list($field_labels, 'id', 'id')) ?></th>
                        <th style="width:6%">{{ get_value_from_list($field_labels, 'sub_type', 'sub_type') }}</th>
                        <th style="width:8%">{{ get_value_from_list($field_labels, 'from_user_id', 'from_user_id') }}</th>
                        <th style="width:8%">{{ get_value_from_list($field_labels, 'notifiable_id', 'notifiable_id') }}</th>                        
                        <th style="width:30%">Msg</th>
                        <th style="width:20%">Date Time</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <th scope="row">{{ $record->id }}</th>
                            <td>{{ get_value_from_list($notification_type_list, $record->sub_type) }}</td>
                            <td>{{ get_value_from_list($from_list, $record->from_user_id) }}</td>
                            <td>{{ get_value_from_list($to_user_list, $record->notifiable_id) }}</td>
                            <td>
                                {{ $record->data['msg'] ?? "NA" }}

                                @if(isset($record->data['link']) && $record->data['link'])
                                    <a href="{{ $record->data['link'] }}">Open Link</a>
                                @endif
                            </td>                            
                            <td>
                                <x-Backend.celec-date-time :value="$record->created_at" />
                                <br/>
                                ({{ $record->created_at_original->diffForHumans() }})
                            </td>   
                            <td>
                            @if(isset($record->data) && is_array($record->data))
                                <div id="data-{{ $record->id }}" class="mx-h-200">
                                    {!! array_print($record->data, 80) !!}
                                </div>

                                <span class="btn btn-info btn-sm css-toggler mt-1 mb-1"
                                    data-sr-css-class-toggle-target="#data-{{ $record->id }}"
                                    data-sr-css-class-toggle-class="mx-h-200" data-sr-css-class-toggle-applied-text="More"
                                    data-sr-css-class-toggle-not-applied-text="Less">
                                    More
                                </span>
                                @endif
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
