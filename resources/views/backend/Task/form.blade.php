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

    <x-Backend.form-errors />

    <x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />
    <div class="row">
        <div class="col-xxxl-10">
            <div class="card">
                <div class="card-header card-no-border pb-0">
                    <a href="{{ route('recurring-task.create') }}">Open Recurring Task Form</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 col-xxl-6">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'is_draft';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.checkbox :id="$field" :name="$field" :label="$label"
                                    value="{{ $model->{$field} }}" />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" mandatory="true" autocomplete="off" />
                            </div>
                            <div class="form-group mb-3">
                                <div class="textarea-wrapper">
                                    <?php
                                    $field = 'description';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.text-area :id="$field" :name="$field" :label="$label" rows="3"
                                        value="{{ $model->{$field} }}" placeholder="Enter {{ $label }}"
                                        class="auto-suggesstion-list" />
                                    <div id="{{ $field . '-suggestion-box' }}" class="suggestion-box hidden"></div>
                                </div>
                            </div>
                            <div class="form-group mb-3 is_draft-block is_draft-block-0">
                                <?php
                                $field = 'dept_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$department_list"
                                    :value="$model->{$field}" class="select2" data-sr-cascade-target="#assign_to_user_id"
                                    data-sr-cascade-url="/user/ajax_get_list?dept_id={v}" mandatory="true" />
                            </div>
                            <div class="form-group mb-3 is_draft-block is_draft-block-0">
                                <?php
                                $field = 'assign_to_user_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    data-value="{{ $model->{$field} }}" mandatory="true" class="select2" mandatory="true" />
                            </div>
                            <div class="form-group mb-3 is_draft-block is_draft-block-0">
                                <?php
                                $field = 'task_type';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$task_type_list"
                                    value="{{ $model->{$field} }}" mandatory="true" class="select2" :pleaseSelect="$false" />
                            </div>
                            <div class="form-group mb-3 is_draft-block is_draft-block-0">
                                <?php
                                $field = 'priority';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <div class="custom-slider mt-4">
                                    <x-Inputs.text-field :id="$field" type="range" :id="$field" :name="$field"
                                        :label="$label" value="{{ $model->{$field} }}" class="custom-slider-input"
                                        preventCssClassAddToInput="true" autocomplete="off" min="1" max="10"
                                        step="1" />

                                    <output class="" for="{{ $field }}" id="{{ $field . '_value' }}"
                                        aria-hidden="true">{{ $model->{$field} }}</output>
                                </div>
                            </div>
                            <div class="row is_draft-block is_draft-block-0">
                                <div class="col-6-1920">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'start_date_time';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        $value = $model->{$field} ?? $default_start_date_time;
                                        if ($value instanceof \Illuminate\Support\Carbon) {
                                            $value = $value->format(DateUtility::DATETIME_OUT_FORMAT);
                                        }
                                        ?>
                                        <label class="form-label">
                                            {{ $label }}
                                            <span class="mandatory">*</span>
                                        </label>
                                        <div class="d-flex">
                                            <div id="start_date_time_preview"></div>
                                            <div class="ml-2">
                                                <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                                                    :value="$value" class="custom-date-picker"
                                                    placeholder="Enter {{ $label }}" autocomplete="off" mandatory="true" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6-1920">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <?php
                                            $field = 'duration';
                                            $label = get_value_from_list($field_labels, $field, $field);
                                            ?>
                                            <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                                                :value="$model->{$field}" mandatory="true" class="validate-int" />
                                        </div>
                                        <div class="col-sm-8">
                                            <?php
                                            $field = 'duration_type';
                                            $label = get_value_from_list($field_labels, $field, $field);
                                            ?>
                                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                                :list="$duration_type_list" :value="$model->{$field}" mandatory="true" class="select2"
                                                :pleaseSelect="$false" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3 is_draft-block is_draft-block-0">
                                <?php
                                $field = 'tag_name';
                                $label = get_value_from_list($field_labels, $field, 'Tags');
                                $value = implode(',', $model->tags?->pluck('name')->toArray()) ?? [];
                                ?>
                                <x-Inputs.choice :id="$field" :name="$field" :label="$label" :value="$value"
                                    class="choice " placeholder="Enter {{ $label }}" />
                            </div>
                            <div class="form-group mb-3 is_draft-block is_draft-block-0">
                                <?php
                                $field = "taskUser";
                                $label = get_value_from_list($field_labels, $field, $field);
                                $value = implode(',', $model->taskUser?->pluck('user_id')->toArray());
                                ?>
                                <x-Inputs.drop-down name="taskUser[]" :label="$label" :list="$user_list" :value="$value"
                                    class="select2" multiple="multiple"  />

                                <?php
                                $field = 'is_send_notification';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.checkbox :id="$field" :name="$field" :label="$label"
                                    value="{{ $model->{$field} }}" />
                            </div>
                            <div class="form-group mb-3 is_draft-block is_draft-block-0">
                                <?php
                                $field = "taskParty";
                                $label = get_value_from_list($field_labels, $field, $field);
                                $value = implode(',', $model->taskParty?->pluck('party_id')->toArray());
                                ?>
                                <x-Inputs.drop-down name="taskParty[]" :label="$label" :list="$party_list" :value="$value"
                                    class="select2" multiple="multiple"  />
                            </div>
                            <div class="form-group mb-3 is_draft-block is_draft-block-0">
                                <?php
                                $field = "taskFile";
                                $label = get_value_from_list($field_labels, $field, $field);
                                $value = implode(',', $model->taskFile?->pluck('document_file_id')->toArray());
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label" :value="$value" multiple="multiple"  />                        
                                <span id="files-choose" class="btn btn-secondary">Choose Files</span>
                            </div>
                        </div>
                        <div class="col-md-12 col-xxl-6">
                                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-8">
                            <x-Backend.form-common-footer-buttons />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>

    <script>        
        $(function() {
            var screen_name = '<?= $screen_name ?>';
            var users = JSON.parse('<?= json_encode($users) ?>');
            $.events.execAtLast(function() {

                var start_date_picker;

                function show_hide_field() {

                    var is_draft = $("#is_draft").prop("checked");
                    is_draft = is_draft ? 1 : 0;

                    var selector = ".is_draft-block";
                    $(selector).hide();
                    $(selector).find("input,select").attr("disabled", true);

                    console.log("hide", selector);

                    var selector = ".is_draft-block-any, .is_draft-block-" + is_draft;

                    if ($(selector).length > 0) {
                        console.log("show", selector);
                        $(selector).show();
                        $(selector).find("input,select").removeAttr("disabled");
                    }
                }

                $("#is_draft").change(show_hide_field);

                start_date_picker = flatpickr("#start_date_time", {
                    enableTime: true,
                    dateFormat: constants.dateTimeFormat,
                    onChange: function(selectedDates, dateStr, instance) {
                        const selectedDate = selectedDates[0];
                        if (selectedDate) {
                            $("#start_date_time_preview").html(get_celec_date_time(
                                selectedDate));
                        }
                    }
                });

                if (screen_name == "add") {
                    start_date_picker.set("minDate", new Date());
                }

                $("#start_date_time_preview").html(get_celec_date_time(start_date_picker.selectedDates[0]));

                $(document).on("input", "#priority", function() {
                    $("#priority_value").val($(this).val());
                });

                show_hide_field();

                suggestion_list($("#description"), $("#description-suggestion-box"), [], "/tags/ajax_get_list");

                $("#dept_id").cascade({
                    beforeGet: function (src, url) {
                        $.loader.init().setInfo("Fetching Users ...").show();
                        return url;
                    },
                    afterGet: function (src, dest, response) {
                        $.loader.init().hide();
                        return response;
                    },
                    default_data : users
                }).trigger("change", {
                    pageLoad: true
                });

                $("#assign_to_user_id").change(function(){
                    var v = $(this).val();

                    if (v)
                    {
                        for(var i in users)
                        {
                            if (users[i]['id'] == v)
                            {
                                $("#dept_id").val(users[i]['dept_id']).trigger('change', {eventBubble : false});
                                return;
                            }
                        }
                    }
                });

                $("#taskFile").browseGallery({
                    opener : "#files-choose"
                });

            });
        })
    </script>
@endsection
