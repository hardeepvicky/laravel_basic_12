<?php
use App\Models\RecurringTask;
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
                    <div class="row is_draft-block is_draft-block-0">
                        <div class="col-md-12 col-lg-6">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'dept_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$department_list"
                                    :value="$model->{$field}" 
                                    class="select2 cascade-drop-down"
                                    data-sr-cascade-target="#assign_to_user_id"
                                    data-sr-cascade-url="/user/ajax_get_list?=dept_id={v}" 
                                    mandatory="true"
                                />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'assign_to_user_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" data-value="{{ $model->{$field} }}" mandatory="true" class="select2" mandatory="true"/>
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'task_type';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$task_type_list" value="{{ $model->{$field}  }}" mandatory="true" class="select2" />
                            </div>
                            <div class="form-group mb-3 ">
                                <?php
                                $field = 'recurring_type';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$recurring_type_list" value="{{ $model->{$field}  }}" mandatory="true" class="select2" />
                            </div>
                            <div class="form-group mb-3 recurring_type_non_custom">
                                <?php
                                $field = 'start_date_time';
                                $label = get_value_from_list($field_labels, $field, $field);
                                $value = $model->{$field};
                                if ($value instanceof  \Illuminate\Support\Carbon)
                                {
                                    $value = $value->format(DateUtility::DATETIME_OUT_FORMAT);
                                }
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                                    :value="$value" class="custom-date-picker" placeholder="Enter {{ $label }}"
                                    autocomplete="off" mandatory="true" readonly="true" />
                            </div>
                            <div class="form-group mb-3 ">
                                <?php
                                $field = 'task_should_be_complete_in';
                                $label = get_value_from_list($field_labels, $field, $field);
                                $false = false
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$complete_in_list" value="{{ $model->{$field}  }}" mandatory="true" class="select2"/>
                            </div>
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
                        
                        <div class="col-md-12 col-lg-12">
                            <div id="recurring_type_block_custom" class="col-md-6 col-lg-4 ">
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
                                                <x-inputs.text-field type="hidden" name="recurringTaskDate[(sr-id)][id]"
                                                    label="" />
                                            </td>
                                            <td>
                                                <x-inputs.text-field name="recurringTaskDate[(sr-id)][date]" label="" class="will-date-picker"
                                                    placeholder="Date" />
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
                                                        <input type="hidden" name="recurringTaskDate[{{ $k }}][id]"
                                                            value="{{ $task_date_arr['id'] ?? "" }}" />
                                                    </td>
                                                    <td>
                                                        <?php $value = $task_date_arr['date'] ?? ""; ?>
                                                        <x-inputs.text-field name="recurringTaskDate[{{ $k }}][date]" class="date-picker"
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
                    </div>
                    <div class="row is_draft-block is_draft-block-0">
                        <div class="col-md-6 col-lg-4">
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
                    dateFormat: constants.dateTimeFormat,
                    enableTime: true,
                });

                if (screen_name == "add")
                {
                    start_date_picker.set("minDate", new Date());
                }

                $("#recurring_type").change(function() {
                    var v = $(this).val();

                    $("#recurring_type_block_custom").find("input,select").attr("disabled", "true");
                    $("#recurring_type_block_custom").hide();

                    $(".recurring_type_non_custom").find("input,select").removeAttr("disabled");
                    $(".recurring_type_non_custom").show();

                    if (v) {
                        if (v == '{{ RecurringTask::RECURRING_CUSTOM }}')
                        {
                            $("#recurring_type_block_custom").find("input,select").removeAttr("disabled");
                            $("#recurring_type_block_custom").show();

                            $(".recurring_type_non_custom").find("input,select").attr("disabled", "true");
                            $(".recurring_type_non_custom").hide();
                        }
                    }
                });

                $(".template-table").srTableTemplate({
                    brace_type: "(",
                    afterRowAdd(_table, last_id, _tr)
                    {
                        _tr.find(".will-date-picker").flatpickr({
                            dateFormat: constants.dateTimeFormat,
                            enableTime: true,
                        });
                    }
                });

                $(document).on("input", "#priority", function() {
                    $("#priority_value").val($(this).val());
                });

                $("#is_draft").change(show_hide_field);

                $(".cascade-drop-down").trigger("change", {pageLoad : true});

                show_hide_field();
            })
        })
    </script>
@endsection
