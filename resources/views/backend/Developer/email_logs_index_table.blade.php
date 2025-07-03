<div class="card" id="page-summary">
    <div class="card-header border-t-info">
        <x-backend.pagination-links :records="$records" />
    </div>

    <div class="card-body">
        <table class="table table-striped table-bordered table-hover mb-0">
            <thead>
                <tr>
                    <th>From Name</th>
                    <th>From Email</th>
                    <th>To Name</th>
                    <th>To Email</th>
                    <th>Subject</th>
                    <th>
                        <?= sortable_anchor('created_at', 'Created') ?>
                        | <?= sortable_anchor('updated_at', 'Updated') ?>
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $record)
                    <tr>
                        <td>{{ $record->from_name }}</td>
                        <td>{{ $record->from_email }}</td>
                        <td>{{ $record->to_name }}</td>
                        <td>{{ $record->to_email }}</td>
                        <td>{{ $record->subject }}</td>
                        <td>
                            <x-Backend.index-table-info :record="$record" :userList="$userListCache" />
                        </td>
                        <td>
                            <span class="btn btn-info btn-sm css-toggler ajax-load"
                                data-sr-css-class-toggle-target="#record-{{ $record->id }}"
                                data-sr-css-class-toggle-class="hidden"
                                data-sr-ajax-load-url="/{{ $record->content_file }}"
                                data-sr-ajax-load-target="#record-{{ $record->id }} .details">
                                Details
                            </span>
                        </td>
                    </tr>
                    <tr id="record-{{ $record->id }}" class="hidden">
                        <td></td>
                        <td class="details" style="background-color:#EEE" colspan="5">

                        </td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <x-backend.pagination-links :records="$records" />
    </div>
</div>
