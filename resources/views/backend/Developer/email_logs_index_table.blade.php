<div class="card" id="page-summary">
    <div class="card-header border-t-info">
        <x-backend.pagination-links :records="$records" />
    </div>

    <div class="card-body">
        <table class="table table-striped table-bordered table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:10%">From Name</th>
                    <th style="width:10%">From Email</th>
                    <th style="width:10%">To Name</th>
                    <th style="width:10%">To Email</th>
                    <th style="width:10%">Subject</th>
                    <th style="width:6%">
                        <?= sortable_anchor('is_sandbox', 'Sandbox') ?>
                    </th>
                    <th style="width:6%">
                        <?= sortable_anchor('is_sent', 'Status') ?>
                    </th>                    
                    <th style="width:12%">
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
                        <td>{{ $record->is_sandbox ? "Yes" : "No" }}</td>
                        <td>{{ $record->is_sent == 1 ? "Sent" : ($record->is_sent == 0 ? "Fail" : "-") }}</td>
                        <td>
                            
                        </td>
                        <td>
                            <x-Backend.index-table-info :record="$record" :userList="$userListCache" />
                        </td>
                        <td>
                            <span class="btn btn-info btn-sm css-toggler ajax-load"
                                data-sr-css-class-toggle-target="#record-{{ $record->id }}"
                                data-sr-css-class-toggle-class="hidden"
                                data-sr-ajax-load-url="{{ $record->content_file_url }}"
                                data-sr-ajax-load-target="#record-{{ $record->id }} .details">
                                Details
                            </span>
                        </td>
                    </tr>
                    <tr id="record-{{ $record->id }}" class="hidden">
                        <td style="background-color: var(--body--bs-body-bg)"></td>
                        <td class="details" colspan="5">

                        </td>
                        <td colspan="3">
                            @if(isset($record->error_code))
                                <h4 class="mb-2 b-b-danger">
                                    Error Code : {{ $record->error_code }}
                                </h4>

                                <div class="mb-3">
                                    {{ $record->error_msg }}
                                </div>
                            @endif

                            @if(isset($record->api_response_code))
                                <h4 class="mb-2 b-b-danger">
                                    API Error Code : {{ $record->api_response_code }}
                                </h4>
                            @endif
                            
                            @if(isset($record->api_response) && is_array($record->api_response))
                            <div id="api_response-{{ $record->id }}" class="mx-h-200 mb-3">
                                {!! array_print($record->api_response) !!}
                            </div>

                            <span class="btn btn-info btn-sm css-toggler mt-1 mb-1"
                                data-sr-css-class-toggle-target="#api_response-{{ $record->id }}"
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
    <div class="card-footer">
        <x-backend.pagination-links :records="$records" />
    </div>
</div>
