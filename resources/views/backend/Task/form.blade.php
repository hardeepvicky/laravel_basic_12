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
                            <?php
                            $field = 'is_draft';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.checkbox :id="$field" :name="$field" :label="$label"
                                value="{{ $model->{$field} }}" />

                            <?php
                            $field = 'name';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                placeholder="Enter {{ $label }}"
                                layout="horizontal"
                                mandatory="true" 
                                autocomplete="off" />

                            <div class="textarea-wrapper">
                                <?php
                                $field = 'description';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-area :id="$field" :name="$field" :label="$label" rows="3"
                                    value="{{ $model->{$field} }}" placeholder="Enter {{ $label }}"
                                    layout="horizontal"
                                    class="auto-suggesstion-list" />
                                <div id="{{ $field . '-suggestion-box' }}" class="suggestion-box hidden"></div>
                            </div>

                            <div class="is_draft-block is_draft-block-0">
                                <?php
                                $field = 'dept_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$department_list"
                                    :value="$model->{$field}" 
                                    class="select2" 
                                    layout="horizontal"
                                    data-sr-cascade-target="#assign_to_user_id"
                                    data-sr-cascade-url="/user/ajax_get_list?dept_id={v}"
                                    mandatory="true" 
                                />
                            </div>

                            <div class="form-group mb-3 is_draft-block is_draft-block-0">
                                <?php
                                $field = 'assign_to_user_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    data-value="{{ $model->{$field} }}" 
                                    layout="horizontal"
                                    class="select2" 
                                    mandatory="true" />
                            </div>

                            <div class="mb-4 is_draft-block is_draft-block-0">
                                <?php
                                $field = 'priority';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label class="form-label">
                                            {{ $label }}
                                            <span class="mandatory">*</span>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                    
                                        <div class="custom-slider mt-4">
                                            <x-Inputs.text-field :id="$field" type="range" :id="$field" :name="$field"
                                                :label="$false" value="{{ $model->{$field} }}" class="custom-slider-input"
                                                preventCssClassAddToInput="true" autocomplete="off" min="1" max="10"
                                                step="1" />

                                            <output class="" for="{{ $field }}" id="{{ $field . '_value' }}"
                                                aria-hidden="true">{{ $model->{$field} }}</output>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="is_draft-block is_draft-block-0">
                                <div class="form-group mb-3">
                                    <?php
                                    $field = 'start_date_time';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                    $value = $model->{$field} ?? "";
                                    if ($value instanceof \Illuminate\Support\Carbon) {
                                        $value = $value->format(DateUtility::DATETIME_OUT_FORMAT);
                                    }
                                    ?>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <label class="form-label">
                                                {{ $label }}
                                                <span class="mandatory">*</span>
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
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
                                </div>
                            </div>
                            <div class="row is_draft-block is_draft-block-0">
                                <?php
                                    $field = 'duration';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label class="form-label">
                                            {{ $label }}
                                            <span class="mandatory">*</span>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="d-flex align-items-start">
                                            <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$model->{$field}" mandatory="true" class="validate-int" />
                                            <?php
                                            $field = 'duration_type';
                                            $label = get_value_from_list($field_labels, $field, $field);
                                            ?>
                                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                                :list="$duration_type_list" :value="$model->{$field}" mandatory="true" class="select2"
                                                :pleaseSelect="$false" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="is_draft-block is_draft-block-0">
                                <?php
                                $field = 'tag_name';
                                $label = get_value_from_list($field_labels, $field, 'Tags');
                                $value = implode(',', $model->tags?->pluck('name')->toArray() ?? []);
                                ?>
                                <x-Inputs.choice :id="$field" :name="$field" :label="$label" :value="$value"
                                    class="choice" 
                                    layout="horizontal"
                                    placeholder="Enter {{ $label }}" 
                                    />
                            </div>
                            
                            <div class="is_draft-block is_draft-block-0 mb-3">
                                <?php
                                $field = "taskFile";
                                $label = get_value_from_list($field_labels, $field, $field);
                                $value = implode(',', $model->taskFile?->pluck('document_file_id')->toArray() ?? []);
                                ?>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label class="form-label">
                                            {{ $label }}
                                            <span class="mandatory">*</span>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">                                        
                                        <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$value" multiple="multiple"  />                        
                                        <span id="files-choose" class="btn btn-secondary">Choose Files</span>
                                    </div>
                                </div>
                            </div>
                            <div class="is_draft-block is_draft-block-0">
                                <?php
                                $field = "taskUser";
                                $label = get_value_from_list($field_labels, $field, $field);
                                $value = implode(',', $model->taskUser?->pluck('user_id')->toArray() ?? []);
                                ?>
                                <x-Inputs.drop-down name="taskUser[]" :label="$label" :list="$user_list" :value="$value"
                                    class="select2" multiple="multiple" layout="horizontal"  />

                            <div class="row mb-3">
                                <div class="col-sm-3">
                                </div>
                                <div class="col-sm-9">
                                    <?php
                                    $field = 'is_send_notification';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.checkbox :id="$field" :name="$field" :label="$label"
                                        value="{{ $model->{$field} }}" />
                                </div>
                            </div>
                                                        
                            <div class="row is_draft-block is_draft-block-0">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = "contact_type";
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                            :list="$contact_type_list" :value="$model->{$field}" class="select2"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3 contact_type-block contact_type-block_{{ Task::CONTACT_TYPE_PARTY }}">
                                        <?php
                                        $field = "taskParty";
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        $value = implode(',', $model->taskParty?->pluck('party_id')->toArray());
                                        ?>
                                        <x-Inputs.drop-down name="taskParty[]" :label="$label" :list="$party_list" :value="$value"
                                            class="select2" multiple="multiple"  />
                                    </div>
                                    <div class="form-group mb-3 contact_type-block contact_type-block_{{ Task::CONTACT_TYPE_LEAD }}">
                                        <?php
                                        $field = "taskLead";
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        $value = implode(',', $model->taskLead?->pluck('lead_id')->toArray());
                                        ?>
                                        <x-Inputs.drop-down name="taskLead[]" :label="$label" :list="$lead_list" :value="$value"
                                            class="select2" multiple="multiple"  />
                                    </div>
                                    <div class="form-group mb-3 contact_type-block contact_type-block_{{ Task::CONTACT_TYPE_CUSTOMER }}">
                                        <?php
                                        $field = "taskCustomer";
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        $value = implode(',', $model->taskCustomer?->pluck('lead_id')->toArray());
                                        ?>
                                        <x-Inputs.drop-down name="taskCustomer[]" :label="$label" :list="$customer_list" :value="$value"
                                            class="select2" multiple="multiple"  />
                                    </div>
                                    <div class="form-group mb-3 contact_type-block contact_type-block_{{ Task::CONTACT_TYPE_DEALER }}">
                                        <?php
                                        $field = "taskDealer";
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        $value = implode(',', $model->taskDealer?->pluck('dealer_id')->toArray());
                                        ?>
                                        <x-Inputs.drop-down name="taskDealer[]" :label="$label" :list="$dealer_list" :value="$value"
                                            class="select2" multiple="multiple"  />
                                    </div>
                                </div>
                            </div>
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

                    var contact_type = $("#contact_type").val();

                    var selector = ".contact_type-block";
                    $(selector).hide();
                    $(selector).find("input,select").attr("disabled", true);

                    console.log("hide", selector);

                    var selector = ".contact_type-block_any";

                    if ($(selector).length > 0) {
                        console.log("show", selector);
                        $(selector).show();
                        $(selector).find("input,select").removeAttr("disabled");
                    }

                    if (contact_type)
                    {
                        var selector = ".contact_type-block_" + contact_type;

                        if ($(selector).length > 0) {
                            console.log("show", selector);
                            $(selector).show();
                            $(selector).find("input,select").removeAttr("disabled");
                        }
                    }
                }

                $("#is_draft").change(show_hide_field);
                $("#contact_type").change(show_hide_field);

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
                }).trigger("sr-cascade.change", {
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
