<?php

use App\Models\Task;
use App\Helpers\DateUtility;

$false = false;
?>

@extends($layout)

@section('content')
    <?php
        $page_header_links = [['title' => 'Summary', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />
    <div class="row">
        <div class="col-xxxl-10">
            <div class="card">                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 col-xxl-6">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'is_draft';
                                $label = 'Draft';
                                ?>
                                <x-Inputs.checkbox :id="$field" :name="$field" :label="$label"
                                    value="{{ $model->{$field} }}" />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'name';
                                $label = 'Name';
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" mandatory="true" autocomplete="off" />
                            </div>
                            <div class="form-group mb-3">
                                <div class="textarea-wrapper">
                                    <?php
                                    $field = 'description';
                                    $label = 'Description';
                                    ?>
                                    <x-Inputs.text-area :id="$field" :name="$field" :label="$label" rows="3"
                                        value="{{ $model->{$field} }}" placeholder="Enter {{ $label }}"
                                        class="auto-suggesstion-list" />
                                    <div id="{{ $field . '-suggestion-box' }}" class="suggestion-box hidden"></div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <div class="form-group mb-3 is_draft-block is_draft-block-0">
                                    <?php
                                    $field = 'assign_to_user_id';
                                    $label = 'Assign To';
                                    ?>
                                    <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$user_list"
                                        :value="$model->{$field}" mandatory="true" class="select2" mandatory="true" />
                                </div>
                            </div>
                            <div class="form-group mb-3 is_draft-block is_draft-block-0">
                                <?php
                                $field = 'priority';
                                $label = 'Priority';
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
                                <div class="form-group mb-3">
                                    <?php
                                    $field = 'start_date_time';
                                    $label = 'Start Date Time';
                                    $value = $model->{$field} ?? "";
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
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?php
                                        $field = 'duration';
                                        $label = 'Duration';
                                        ?>
                                        <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                                            :value="$model->{$field}" mandatory="true" class="validate-int" />
                                    </div>
                                    <div class="col-sm-8">
                                        <?php
                                        $field = 'duration_type';
                                        $label = 'Duration Type';
                                        ?>
                                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                            :list="$duration_type_list" :value="$model->{$field}" mandatory="true" class="select2"
                                            :pleaseSelect="$false" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3 is_draft-block is_draft-block-0">
                                <?php
                                $field = "taskFile";
                                $label = 'Files';
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
                    time_24hr: true,
                    defaultDate: new Date(),
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

                $("#taskFile").browseGallery({
                    opener : "#files-choose"
                });

            });
        })
    </script>
@endsection
