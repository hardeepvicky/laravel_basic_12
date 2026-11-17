<?php
use App\Models\RecurringTask;
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
                            <x-Inputs.text-area :id="$field" :name="$field" :label="$label"
                                value="{{ $model->{$field} }}" rows="8" placeholder="Enter {{ $label }}"
                                class="auto-suggesstion-list" />
                            <div id="{{ $field . '-suggestion-box' }}" class="suggestion-box hidden"></div>
                        </div>
                    </div>
                    <div class="form-group mb-3 is_draft-block is_draft-block-0">
                        <?php
                        $field = 'assign_to_user_id';
                        $label = 'Assign To';
                        ?>
                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$user_list"
                            :value="$model->{$field}" mandatory="true" class="select2" mandatory="true" />
                    </div>                   
                    <div class="form-group mb-3  is_draft-block is_draft-block-0">
                        <?php
                        $field = 'recurring_type';
                        $label = 'Recurring Type';
                        ?>
                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$recurring_type_list"
                            value="{{ $model->{$field} }}" mandatory="true" class="select2" />
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
                    </div>

                    
                    <div class="form-group mb-3 is_draft-block is_draft-block-0">
                        <?php
                        $field = 'is_send_notification';
                        $label = 'Send Notification';
                        ?>
                        <x-Inputs.checkbox :id="$field" :name="$field" :label="$label"
                            value="{{ $model->{$field} }}" />
                    </div>
                    <div class="form-group mb-3 is_draft-block is_draft-block-0">
                        <?php
                        $field = "taskFile";
                        $label = 'Files';
                        $value = implode(',', $model->recurringTaskFile?->pluck('document_file_id')->toArray());
                        ?>
                        <x-Inputs.text-field :id="$field" :name="$field" :label="$label" :value="$value" multiple="multiple"  />                        
                        <span id="files-choose" class="btn btn-secondary">Choose Files</span>
                    </div>
                </div>

                <div class="col-md-12 col-xxl-6 is_draft-block is_draft-block-0">
                    <div id="recurring_type_block_custom">
                        <div class="form-group mb-3">
                            <table class="table table-sm table-bordered order-column template-table"
                                data-sr-table-template-min-row="0">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width : 50px;">
                                            <span class="sr-table-template-add">
                                                <i class="fas fa-plus-circle text-success icon"></i>
                                            </span>
                                        </th>
                                        <th>Date</th>
                                        <th>Preview</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="sr-table-template-row hidden">
                                        <td>
                                            <div class="block">
                                                <div class="left-block">

                                                </div>
                                                <div class="right-block">
                                                    <span class="sr-table-template-delete">
                                                        <i class=" fas fa-times-circle text-danger icon"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <x-inputs.text-field type="hidden" name="recurringTaskDate[(sr-id)][id]"
                                                label="" />
                                        </td>
                                        <td>
                                            <x-inputs.text-field name="recurringTaskDate[(sr-id)][date]" label=""
                                                class="will-date-time-picker" placeholder="Date"
                                                data-preview-on="#recurringTaskDate-(sr-id)-date-preview" />
                                        </td>
                                        <td>
                                            <div id="recurringTaskDate-(sr-id)-date-preview"></div>
                                        </td>
                                    </tr>
                                    @php
                                        $recurringTaskDate = old('recurringTaskDate', $recurringTaskDate ?? []);
                                        unset($recurringTaskDate['(sr-id)']);
                                    @endphp
                                    @if (isset($recurringTaskDate))
                                        @foreach ($recurringTaskDate as $k => $task_date_arr)
                                            <tr class="" sr-id="{{ $task_date_arr['id'] }}"
                                                sr-counter="{{ $k }}">
                                                <td>
                                                    <div class="block">
                                                        <div class="left-block">
                                                        </div>
                                                        <div class="right-block">
                                                            <span class="sr-table-template-delete">
                                                                <i class=" fas fa-times-circle text-danger icon"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <input type="hidden"
                                                        name="recurringTaskDate[{{ $k }}][id]"
                                                        value="{{ $task_date_arr['id'] ?? '' }}" />
                                                </td>
                                                <td>
                                                    <?php $value = $task_date_arr['date'] ?? ''; ?>
                                                    <x-inputs.text-field
                                                        name="recurringTaskDate[{{ $k }}][date]"
                                                        class="custom-date-time-picker" label="" :value="$value"
                                                        placeholder="Date"
                                                        data-preview-on="#recurringTaskDate-{{ $k }}-date-preview" />
                                                </td>
                                                <td>
                                                    <div id="recurringTaskDate-{{ $k }}-date-preview">
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="text-center" style="width : 80px;">
                                            <span class="sr-table-template-add">
                                                <i class="fas fa-plus-circle text-success icon"></i>
                                            </span>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
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

                    $("#recurring_type").trigger("change");
                }

                start_date_picker = flatpickr("#start_date_time", {
                    enableTime: true,
                    time_24hr: true,
                    dateFormat: constants.dateTimeFormat,
                    onChange: function(selectedDates, dateStr, instance) {
                        const selectedDate = selectedDates[0];
                        if (selectedDate) {
                            $("#start_date_time_preview").html(get_celec_date_time(
                                selectedDate));
                        }
                    }
                });

                if (start_date_picker) {
                    if (screen_name == "add") {
                        start_date_picker.set("minDate", new Date());
                    }

                    $("#start_date_time_preview").html(get_celec_date_time(start_date_picker.selectedDates[
                        0]));
                }

                $("#recurring_type").change(function() {
                    var v = $(this).val();

                    $("#recurring_type_block_custom").find("input,select").attr("disabled", "true");
                    $("#recurring_type_block_custom").hide();

                    $(".recurring_type_non_custom").find("input,select").removeAttr("disabled");
                    $(".recurring_type_non_custom").show();

                    if (v) {
                        if (v == '{{ RecurringTask::RECURRING_CUSTOM }}') {
                            $("#recurring_type_block_custom").find("input,select").removeAttr(
                                "disabled");
                            $("#recurring_type_block_custom").show();

                            $(".recurring_type_non_custom").find("input,select").attr("disabled",
                                "true");
                            $(".recurring_type_non_custom").hide();
                        }
                    }
                });

                $(".template-table").srTableTemplate({
                    brace_type: "(",
                    afterRowAdd(_table, last_id, _tr) {
                        _tr.find(".will-date-picker").flatpickr({
                            dateFormat: constants.dateFormat,
                        });

                        custom_date_time_picker_apply(_tr.find(".will-date-time-picker"));
                    }
                });

                custom_date_time_picker_apply($(".custom-date-time-picker"));

                $(document).on("input", "#priority", function() {
                    $("#priority_value").val($(this).val());
                });

                $("#is_draft").change(show_hide_field);

                $(".cascade-drop-down").trigger("sr-cascade.change", {
                    pageLoad: true
                });

                $("#taskFile").browseGallery({
                    opener : "#files-choose"
                });

                show_hide_field();
            })
        });

        function custom_date_time_picker_apply(element) {
            if ($(element).length == 0) {
                console.warn(`custom_date_time_picker_apply : jquery could not found ${element}`);
            }

            $(element).each(function() {
                var preview_selector = $(this).data("preview-on");

                if (preview_selector) {
                    var previewEle = $(preview_selector);

                    if (previewEle.length == 0) {
                        console.error(`custom_date_time_picker_apply : jquery could not found ${preview_selector}`);
                    }
                } else {
                    var previewEle = $(this).parent().find(".date-time-preview");
                }

                if (previewEle.length == 0) {
                    console.warn("custom_date_time_picker_apply : could not found preview element");
                }

                const onChangeDateTime = (selectedDates, dateStr, instance) => {
                    const selectedDate = selectedDates[0];
                    if (selectedDate && previewEle.length > 0) {
                        previewEle.html(get_celec_date_time(selectedDate));
                    }
                };

                var fp = flatpickr($(this), {
                    dateFormat: constants.dateTimeFormat,
                    enableTime: true,
                    onChange: onChangeDateTime
                });

                onChangeDateTime(fp.selectedDates);
            });


        }
    </script>
@endsection
