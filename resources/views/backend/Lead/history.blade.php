<?php

use App\Models\Task;
use App\Helpers\DateUtility;

$false = false;
?>

@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    @include($viewPrefix . '.show')

    <x-Backend.form-errors />

    <div class="card">
        <div class="card-header card-no-border pb-0">
            History

            <div class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modal-call-form">Call</div>
            <div class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modal-sms-form">SMS</div>
            <div class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modal-whatsapp-form">Whatsapp</div>
            <div class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modal-email-form">Email</div>
            <div class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modal-snooze-form">Snooze</div>
            <div class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modal-dispatch-form">Dispatch</div>
            <div class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modal-transfer-form">Transfer Lead</div>
            <div class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modal-convert-form">Convert To Customer</div>
            <div class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modal-label-download">Label</div>
            <div class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modal-label-a4-download">Label A4</div>

            <div class="card-header-right">
                <ul class="list-unstyled card-option" style="width: 230px;">
                    <li><i class="fa-solid fa-maximize full-card"></i></li>
                    <li><i class="fa-solid fa-minus minimize-card"></i></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered summary-table">
                    <thead>
                        <tr class="border-bottom-primary">
                            <th><?= get_value_from_list($history_field_labels, 'id', 'id') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'entry_by', 'entry_by') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'action_type', 'action_type') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'response_type_id', 'response_type_id') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'status_type', 'status_type') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'assing_to_user_id', 'assing_to_user_id') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'recording_file', 'recording_file') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'remarks', 'remarks') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'next_meeting_date_time', 'next_meeting_date_time') ?></th>
                            <th>{{ get_value_from_list($history_field_labels, 'created_at', 'created_at') }}</th>
                            <th>{{ get_value_from_list($history_field_labels, 'updated_at', 'updated_at') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($record->history as $record)
                        <?php
                            $record->convertTimeZoneForDisplay();
                        ?>
                            
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
