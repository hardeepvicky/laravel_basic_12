<?php
use App\Models\Task;
use App\Helpers\DateUtility;
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
        <div class="col-md-12 offset-lg-1 col-lg-10">
            <div class="card">
                <div class="card-header card-no-border pb-0">
                    <h3>Basic</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <?php
                            $field = 'is_draft';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.checkbox :id="$field" :name="$field" :label="$label" value="{{ $model->{$field}  }}"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field}  }}"
                                    placeholder="Enter {{ $label }}" mandatory="true" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'description';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-area :name="$field" :label="$label" value="{{ $model->{$field}  }}"
                                    placeholder="Enter {{ $label }}" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-4 is_draft-block is_draft-block-0">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'type';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$type_list" value="{{ $model->{$field}  }}" mandatory="true" class="select2" />
                            </div>
                        </div>
                        <div
                            class="col-md-6 col-lg-4 is_draft-block is_draft-block-0 type-block type-block-{{ Task::TYPE_ONE_TIME }}">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'one_time_type';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$one_time_type_list" value="{{ $model->{$field}  }}" mandatory="true" class="select2" />
                                <div id="date-field-info"></div>
                            </div>
                        </div>
                        <div
                            class="col-md-6 col-lg-4 is_draft-block is_draft-block-0 type-block type-block-{{ Task::TYPE_RECURRING }}">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'recurring_type';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$recurring_type_list" value="{{ $model->{$field}  }}" mandatory="true" class="select2" />
                            </div>
                        </div>
                        <div
                            class="col-md-6 col-lg-4 is_draft-block is_draft-block-0">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'complete_time_in_days';
                                $label = get_value_from_list($field_labels, $field, $field);
                                $false = false
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$complete_in_days_list" value="{{ $model->{$field}  }}" mandatory="true" class="select2" :pleaseSelect="$false"/>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 is_draft-block is_draft-block-0">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'assign_to_user_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$user_list" value="{{ $model->{$field}  }}" mandatory="true" class="select2" />
                            </div>
                        </div>
                        <div
                            class="col-md-6 col-lg-4 date-field-blocks is_draft-block is_draft-block-0 type-block type-block-{{ Task::TYPE_ONE_TIME }}">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'start_date_time';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                                    value="{{ $model->{$field}  }}" class="custom-date-picker" placeholder="Enter {{ $label }}"
                                    autocomplete="off" mandatory="true" readonly="true" />
                            </div>
                        </div>
                        <div
                            class="col-md-6 col-lg-4 date-field-blocks is_draft-block is_draft-block-0 type-block type-block-{{ Task::TYPE_ONE_TIME }}">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'end_date_time';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                                    value="{{ $model->{$field}  }}" class="custom-date-picker" placeholder="Enter {{ $label }}"
                                    autocomplete="off" mandatory="true" readonly="true" />
                            </div>
                        </div>
                        <div id="recurring_type_block_custom" class="col-md-6 col-lg-4 type-block type-block-{{ Task::TYPE_RECURRING }}">
                            <div class="form-group mb-3">
                                <table class="table table-sm table-bordered order-column template-table"
                                    data-sr-table-template-min-row="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width : 80px;">
                                                <span class="sr-table-template-add">
                                                    <i class="fas fa-plus-circle text-success icon"></i>
                                                </span>
                                            </th>
                                            <th>Date</th>
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
                                                <x-inputs.text-field type="hidden" name="taskRecurringDate[(sr-id)][id]"
                                                    label="" />
                                            </td>
                                            <td>
                                                <x-inputs.text-field name="taskRecurringDate[(sr-id)][date]" label="" class="will-date-picker"
                                                    placeholder="Date" />
                                            </td>
                                            
                                        </tr>
                                        @php
                                            $taskRecurringDate = old('taskRecurringDate', $taskRecurringDate ?? []);
                                            unset($taskRecurringDate['(sr-id)']);
                                        @endphp
                                        @if (isset($taskRecurringDate))
                                            @foreach ($taskRecurringDate as $k => $task_date_arr)
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
                                                        <input type="hidden" name="taskRecurringDate[{{ $k }}][id]"
                                                            value="{{ $task_date_arr['id'] ?? "" }}" />
                                                    </td>
                                                    <td>
                                                        <?php $value = $task_date_arr['date'] ?? ""; ?>
                                                        <x-inputs.text-field name="taskRecurringDate[{{ $k }}][date]" class="date-picker"
                                                            label="" :value="$value" placeholder="Date" />
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
                        <div class="col-md-6 col-lg-4  is_draft-block is_draft-block-0">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'priority';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <div class="custom-slider mt-4">
                                    <x-Inputs.text-field :id="$field" type="range" :id="$field"
                                        :name="$field" :label="$label" value="{{ $model->{$field}  }}" class="custom-slider-input"
                                        preventCssClassAddToInput="true" autocomplete="off" min="1" max="10"
                                        step="1" />

                                    <output class="" for="{{ $field }}" id="{{ $field . '_value' }}"
                                        aria-hidden="true">{{ $model->{$field} }}</output>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-4 is_draft-block is_draft-block-0">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'is_send_notification';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.checkbox :name="$field" :label="$label" value="{{ $model->{$field}  }}"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-Backend.form-common-footer-buttons />
        </div>
    </div>
    </form>

    <script>
        $(function() {
            $.events.execAtLast(function() {

                var start_date_picker, end_date_picker;

                var one_time_type_list = JSON.parse('<?= json_encode($one_time_type_list) ?>');
                var recurring_type_list = JSON.parse('<?= json_encode($recurring_type_list) ?>');

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

                    var type = $("#type:visible").val();

                    var selector = ".type-block";

                    $(".type-block").hide();
                    $(".type-block").find("input,select").attr("disabled", true);

                    console.log("hide", selector);

                    var selector = ".type-block-any, .type-block-" + type;

                    if ($(selector).length > 0) {
                        console.log("show", selector);
                        $(selector).show();
                        $(selector).find("input,select").removeAttr("disabled");
                    }

                    $("#one_time_type").trigger("change");

                    $("#recurring_type").trigger("change");
                }

                function cal_start_end_date()
                {
                    var when_to_start = $("#one_time_type").val();

                    $(".date-field-blocks").hide();
                    $("#date-field-info").show();

                    const today = new Date();
                    var days = 0;
                    const futureDate = new Date(today);

                    if (when_to_start)
                    {
                        switch (when_to_start) {
                            case '{{ Task::ONE_TIME_IMMDIATE }}':
                                break;

                            case '{{ Task::ONE_TIME_AFTER_1_DAY }}':
                                days = 1;
                                break;

                            case '{{ Task::ONE_TIME_AFTER_2_DAY }}':
                                days = 2;
                                break;

                            case '{{ Task::ONE_TIME_AFTER_3_DAY }}':
                                days = 3;                                
                                break;

                            case '{{ Task::ONE_TIME_AFTER_WEEK }}':
                                days = 7;
                                break;

                            case '{{ Task::ONE_TIME_AFTER_MONTH }}':
                                days = 30;
                                break;

                            case '{{ Task::ONE_TIME_CUSTOM }}':
                                $(".date-field-blocks").show();
                                $("#date-field-info").hide();                                
                                break;

                            default:
                                $.events.onError("switch case not found", v + " in one_time_type");
                                break;
                        }

                        futureDate.setDate(today.getDate() + days);
                        start_date_picker.setDate(futureDate);

                        var days = 0;

                        var complete_time_in_days = $("#complete_time_in_days").val();

                        switch(complete_time_in_days)
                        {
                            case '{{ Task::COMPLETE_TIME_1_DAY }}':
                                days = 1;
                                break;
                            case '{{ Task::COMPLETE_TIME_2_DAY }}':
                                days = 2;
                                break;
                            case '{{ Task::COMPLETE_TIME_3_DAY }}':
                                days = 3;
                                break;
                            case '{{ Task::COMPLETE_TIME_4_DAY }}':
                                days = 4;
                                break;
                            case '{{ Task::COMPLETE_TIME_5_DAY }}':
                                days = 5;
                                break;
                            case '{{ Task::COMPLETE_TIME_WEEK }}':
                                days = 7;
                                break;
                            case '{{ Task::COMPLETE_TIME_2_WEEK }}':
                                days = 14;
                                break;
                            case '{{ Task::COMPLETE_TIME_MONTH }}':
                                days = 30;
                                break;
                            case '{{ Task::COMPLETE_TIME_QUARTER }}':
                                days = 90;
                                break;
                            default:
                                $.events.onError("switch case not found", v + " in one_time_type");
                                break;
                        }

                        futureDate.setDate(futureDate.getDate() + days);
                        end_date_picker.setDate(futureDate);
                    }
                    else
                    {
                        start_date_picker.clear();
                        end_date_picker.clear();
                    }

                    var info_list = {
                        "Start Date": [],
                        "End Date": [],
                    };

                    for(var i in start_date_picker.selectedDates)
                    {
                        var dateObj = start_date_picker.selectedDates[i];
                        info_list["Start Date"].push(start_date_picker.formatDate(dateObj,'{{ DateUtility::DATE_OUT_FORMAT }}'));
                    }

                    for(var i in end_date_picker.selectedDates)
                    {
                        var dateObj = end_date_picker.selectedDates[i];
                        info_list["End Date"].push(end_date_picker.formatDate(dateObj,'{{ DateUtility::DATE_OUT_FORMAT }}'));
                    }

                    var html = "";

                    for(var label in info_list)
                    {
                        var item = info_list[label];
                        if (item && item.length > 0)
                        {
                            html +=`<label class="info">${label} : <span>${item.join(",")}</span></label>`;
                        }
                    }

                    $("#date-field-info").html(html);
                }

                $("#type").change(show_hide_field);

                $("#is_draft").change(show_hide_field);

                start_date_picker = flatpickr("#start_date_time", {
                    dateFormat: constants.dateFormat,
                });

                end_date_picker = flatpickr("#end_date_time", {
                    dateFormat: constants.dateFormat,
                });

                $("#one_time_type, #complete_time_in_days").change(function() {
                    cal_start_end_date();
                });

                $("#recurring_type").change(function() {
                    var v = $(this).val();

                    $("#recurring_type_block_custom").find("input,select").attr("disabled", "true");
                    $("#recurring_type_block_custom").hide();

                    if (v) {
                        if (v == '{{ Task::RECURRING_CUSTOM }}')
                        {
                            $("#recurring_type_block_custom").find("input,select").removeAttr("disabled");
                            $("#recurring_type_block_custom").show();
                        }
                    }
                });

                $(".template-table").srTableTemplate({
                    brace_type: "(",
                    afterRowAdd(_table, last_id, _tr)
                    {
                        _tr.find(".will-date-picker").flatpickr({
                            dateFormat: constants.dateFormat,
                        });
                    }
                });

                $(document).on("input", "#priority", function() {
                    $("#priority_value").val($(this).val());
                });

                show_hide_field();
            })
        })
    </script>
@endsection
