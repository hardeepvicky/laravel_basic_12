<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr class="border-bottom-primary">
                        <th><?= sortable_anchor('name', 'Name') ?></th>
                        <th>Message</th>
                        <th>Contact Info</th>
                        <th>Lead Info</th>
                        <th>Info</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->message }}</td>
                            <td>
                                <label class="info">
                                    Mobile :
                                    <span>{{ $record->mobile }}</span>
                                </label>
                                <label class="info">
                                    Email :
                                    <span>{{ $record->email }}</span>
                                </label>
                                <label class="info">
                                    Address :
                                    <span>{{ $record->address }}</span>
                                    <span>{{ $country_state_city_list[$record->city_id] ?? '' }}</span>
                                </label>
                            </td>
                            <td>
                                <label class="info">
                                    Source :
                                    <span>{{ $source_list[$record->source_id] ?? '' }}</span>
                                </label>
                                <label class="info">
                                    Type :
                                    <span>{{ $type_list[$record->type_id] ?? '' }}</span>
                                </label>
                                <label class="info">
                                    Status :
                                    <span>{{ $status_list[$record->status_id] ?? '' }}</span>
                                </label>
                                <label class="info">
                                    Product Category :
                                    <span>{{ $category_list[$record->category_id] ?? '' }}</span>
                                </label>
                            </td>
                            <td>
                                <x-Backend.index-table-info :record="$record" :userList="$userListCache" />
                            </td>
                            <td>
                                <a class="fancybox" href="{{ $record->file_url }}">
                                    <img src="{{ $record->file_url }}" alt="Image Not Uploaded" class="small-img">
                                </a>
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
