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

    <x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />
    <div class="card">
        <div class="card-header card-no-border pb-0">
            Form

            <div class="card-header-right">
                <ul class="list-unstyled card-option" style="width: 230px;">
                    <li><i class="fa-solid fa-maximize full-card"></i></li>
                    <li><i class="fa-solid fa-minus minimize-card"></i></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 col-xxl-3">
                    <div class="form-group mb-3">
                        <?php
                        $field = 'action_taken_id';
                        $label = get_value_from_list($task_taken_actions_fields, $field, $field);
                        ?>
                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$action_list"
                            :value="$model->{$field}" class="select2" mandatory="true" />
                    </div>
                </div>
                <div class="col-md-6 col-xxl-3">
                    <div class="form-group mb-3">
                        <?php
                        $field = 'response_taken_id';
                        $label = get_value_from_list($task_taken_actions_fields, $field, $field);
                        ?>
                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$response_list"
                            :value="$model->{$field}" class="select2" mandatory="true" />
                    </div>
                </div>
                <div class="col-md-6 col-xxl-3">
                    <div class="form-group mb-3">
                        <?php
                        $field = 'remarks';
                        $label = get_value_from_list($task_taken_actions_fields, $field, $field);
                        ?>
                        <x-Inputs.text-field :id="$field" :name="$field" :label="$label" :value="$model->{$field}" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xxl-8">
                    <div class="form-buttons mb-4">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>

    <div class="card">
        <div class="card-header card-no-border pb-0">
            History
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
                            <th><?= get_value_from_list($task_taken_actions_fields, 'id', 'id') ?></th>
                            <th><?= get_value_from_list($task_taken_actions_fields, 'action_taken_id', 'action_taken_id') ?></th>
                            <th><?= get_value_from_list($task_taken_actions_fields, 'response_taken_id', 'response_taken_id') ?>
                            </th>
                            <th><?= get_value_from_list($task_taken_actions_fields, 'remarks', 'remarks') ?></th>
                            <th>
                                {{ get_value_from_list($field_labels, 'created_at', 'created_at') }}
                                |
                                {{ get_value_from_list($field_labels, 'updated_at', 'updated_at') }}
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($history_records as $record)
                            <tr>
                                <th scope="row">
                                    {{ $record->id }}
                                </th>
                                <td>{{ get_value_from_list($action_list, $record->action_taken_id) }}</td>
                                <td>{{ get_value_from_list($response_list, $record->response_taken_id) }}</td>
                                <td>{{ $record->remarks }}</td>
                                <td>
                                    <x-Backend.index-table-info :record="$record" :userList="$userListCache" />
                                </td>
                                <td>
                                    <x-Backend.summary-delete-button url="{{ route($routePrefix . '.history_destroy', [$record->id]) }}" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
