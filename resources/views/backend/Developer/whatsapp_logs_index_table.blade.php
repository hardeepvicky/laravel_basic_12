<div class="card" id="page-summary">
    <div class="card-header border-t-info">
        <x-backend.pagination-links :records="$records" />
    </div>

    <div class="card-body">
        <table class="table table-striped table-bordered table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:8%">
                        <?= sortable_anchor('api_platform', 'API Platform') ?>
                    </th>
                    <th>
                        <?= sortable_anchor('template_name', 'Template Name') ?>
                    </th>
                    <th style="width:8%">
                        <?= sortable_anchor('template_lanugage', 'Template Lanugage') ?>
                    </th>
                    <th style="width:10%">
                        <?= sortable_anchor('to_name', 'To') ?>
                    </th>
                    <th style="width:6%">
                        <?= sortable_anchor('country_code', 'Country Code') ?>
                    </th>
                    <th style="width:8%">
                        <?= sortable_anchor('to_mobile', 'To Mobile') ?>
                    </th>
                    <th style="width:6%">
                        <?= sortable_anchor('is_sandbox', 'Sandbox') ?>
                    </th>
                    <th style="width:6%">
                        <?= sortable_anchor('is_sent', 'Status') ?>
                    </th>
                    <th style="width:12%">
                        <?= sortable_anchor('created_at', 'Created') ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $record)
                    <tr>
                        <td>{{ $record->api_platform }}</td>
                        <td>{{ $record->template_name }}</td>
                        <td>{{ $record->template_lanugage }}</td>
                        <td>{{ $record->country_code }}</td>
                        <td>{{ $record->to_name }}</td>
                        <td>{{ $record->to_mobile }}</td>
                        <td>{{ get_value_from_list($yes_no_list, $record->is_sandbox) }}</td>
                        <td>{{ $record->is_sent == 1 ? "Sent" : ($record->is_sent == 0 ? "Fail" : "-") }}</td>                        
                        <td>
                            <x-Backend.created-and-by :datetime="$record->created_at_user_tz" :userId="$record->created_by" />

                            <br/><br/>

                            <span class="btn btn-info btn-sm css-toggler mt-1 mb-1"
                                data-sr-css-class-toggle-target="#record-{{ $record->id }}"
                                data-sr-css-class-toggle-class="hidden">
                                Details
                            </span>
                        </td>
                    </tr>
                    <tr id="record-{{ $record->id }}" class="hidden">
                        <td style="background-color: var(--body--bs-body-bg)"></td>
                        <td colspan="6">
                            <div class="row">
                                <div class="col-md-6 col-xxl-4">
                                    <div id="other-info-{{ $record->id }}" class="mx-h-200">
                                        @if (isset($record->header_values) && is_array($record->header_values))
                                            <label class="label label-info">
                                                Header Values
                                            </label>
                                            {!! array_print($record->header_values, 100) !!}
                                        @endif

                                        @if (isset($record->body_values) && is_array($record->body_values))
                                            <label class="label label-info">
                                                Body Values
                                            </label>
                                            {!! array_print($record->body_values, 100) !!}
                                        @endif

                                        @if (isset($record->button_values) && is_array($record->button_values))
                                            <label class="label label-info">
                                                Button Values
                                            </label>
                                            {!! array_print($record->button_values, 100) !!}
                                        @endif
                                    </div>

                                    <span class="btn btn-info btn-sm css-toggler mt-1 mb-1"
                                        data-sr-css-class-toggle-target="#other-info-{{ $record->id }}"
                                        data-sr-css-class-toggle-class="mx-h-200" data-sr-css-class-toggle-applied-text="More"
                                        data-sr-css-class-toggle-not-applied-text="Less">
                                        More
                                    </span>
                                </div>

                                <div class="col-md-6 col-xxl-4">

                                    <h4 class="mb-2 b-b-danger">
                                        Error Code : {{ $record->error_code }}
                                    </h4>

                                    {{ $record->error_msg }}
                                </div>
                            </div>
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
