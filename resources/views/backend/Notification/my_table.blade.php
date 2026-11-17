<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr class="border-bottom-primary">
                        <th style="width:6%"><?= sortable_anchor('id', 'ID') ?></th>
                        <th style="width:8%">
                            <?= sortable_anchor('sub_type', 'Sub Type') ?>
                        </th>
                        <th style="width:10%">From</th>
                        <th>Message</th>
                        <th style="width:20%">
                            <?= sortable_anchor('created_at', 'Date Time') ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <th scope="row">{{ $record->id }}</th>
                            <td>{{ get_value_from_list($notification_type_list, $record->sub_type) }}</td>
                            <td>{{ get_value_from_list($from_list, $record->from_user_id) }}</td>
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
