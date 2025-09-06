@extends($layout)

@section('content')
    <?php
    use App\Helpers\DateUtility;
    
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />

    <div class="row">
        <div class="col-xxxl-12">
            <div class="card">
                <div class="card-header card-no-border pb-0">
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 col-xxl-6">
                            <?php
                                $field = 'hierarchy_type';
                                $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                :list="$type_list"
                                :value="$model->{$field}"
                                :pleaseSelect="$false"
                                layout="horizontal"
                                horizontalSecondColumnLength="3"
                                class="select2"
                                mandatory="true" />

                            <?php
                                $field = 'no_of_serials';
                                $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field layout="horizontal" horizontalSecondColumnLength="3" type="number" :id="$field" :name="$field"
                                :label="$label" :value="$model->{$field}"
                                placeholder="Enter {{ $label }}" mandatory="true" class="validate-int"
                                min="1" max="1000" />

                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <label>
                                        <input type="radio" name="serial_type" value="auto" class="type form-check-input" checked="true">
                                        <span class="fs-5 f-w-500">Auto</span>
                                    </label>
                                </div>
                                <div class="col-sm-9 type-block type-block_auto">
                                    <div class="d-flex align-items-start">
                                        <div style="width:120px">
                                            <?php
                                            $field = 'serial_no';
                                            ?>
                                            <div class="form-group">
                                                <x-Inputs.text-field id="auto_serial_no" :name="$field" :label="$false"
                                                    value="{{ $auto_next_serial_no }}" placeholder="Enter {{ $label }}"
                                                    readonly="true" />
                                            </div>
                                        </div>
                                        <div class="ml-2" style="width:120px">
                                            <?php
                                            $field = 'end_serial_no';
                                            ?>
                                            <div class="form-group">
                                                <x-Inputs.text-field id="auto_end_serial_no" :name="$field" :label="$false"
                                                    readonly="true" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <label>
                                        <input type="radio" name="serial_type" value="manual" class="type form-check-input">
                                        <span class="fs-5 f-w-500">Manual</span>
                                    </label>
                                </div>
                                <div class="col-sm-9 type-block type-block_manual">
                                    <div class="d-flex align-items-start">
                                        <div style="width:120px">
                                            <?php
                                            $field = 'serial_no';
                                            ?>
                                            <div class="form-group">
                                                <x-Inputs.text-field id="manual_serial_no" :name="$field" :label="$false"
                                                    value="{{ $manual_next_serial_no }}" placeholder="Enter {{ $label }}"
                                                    />
                                            </div>
                                        </div>
                                        <div class="ml-2" style="width:120px">
                                            <?php
                                            $field = 'end_serial_no';
                                            ?>
                                            <div class="form-group">
                                                <x-Inputs.text-field id="manual_end_serial_no" :name="$field" :label="$false"
                                                    readonly="true" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                                $field = 'product_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                            ?>

                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <label class="form-label">
                                        {{ $label }}
                                        <span class="mandatory">*</span>
                                    </label>
                                </div>
                                <div class="col-sm-9">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :list="$empty_list" :value="$model->{$field}"  class="select2" mandatory="true" />
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="d-flex align-items-start">
                                                <span id="opener-product_id" class="btn btn-secondary flex-shrink-0">Choose</span>
                                                <input type="hidden" id="choose-product_id" value="{{  $model->product_id }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                                $field = 'make_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                if ($default_maker && !isset($model->{$field}))
                                {
                                    $model->{$field} = $default_maker->id;
                                }
                            ?>

                            <x-Inputs.drop-down :id="$field" :name="$field"
                                :label="$label"
                                :list="$maker_list"
                                :value="$model->{$field}"
                                layout="horizontal"
                                horizontalSecondColumnLength="3"
                                class="select2"
                                mandatory="true"
                            />

                            <?php
                            $field = 'manufacture_date_time';
                            $label = get_value_from_list($field_labels, $field, $field);
                            $value = $model->{$field} ?? "";
                            if ($value instanceof \Illuminate\Support\Carbon) {
                                $value = $value->format(DateUtility::DATETIME_OUT_FORMAT);
                            }
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field"
                                :label="$label"
                                :value="$value"
                                class="custom-date-time-picker"
                                layout="horizontal"
                                horizontalSecondColumnLength="3"
                                placeholder="Enter {{ $label }}"
                                mandatory="true"
                            />
                        </div>
                        <div class="col-md-12 col-xxl-12">
                            <div class="hierarchy_type-block hierarchy_type-block_parent">
                                <h4 class="b-b-info">
                                    Child Serial No.
                                </h4>
                                <table class="table table-bordered order-column template-table"
                                    data-sr-table-template-min-row="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width : 8%;">
                                                <span class="sr-table-template-add">
                                                    <i class="fas fa-plus-circle text-success icon"></i>
                                                </span>
                                            </th>
                                            <th style="width:50%">
                                                {{ get_value_from_list($field_labels, 'product_id') }}
                                            </th>
                                            <th style="width:20%">
                                                {{ get_value_from_list($field_labels, 'make_id') }}
                                            </th>
                                            <th style="width:20%">
                                                {{ get_value_from_list($field_labels, 'serial_no') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="sr-table-template-row hidden">
                                            <td>
                                                <div class="block">
                                                    <div class="left-block">
                                                        <div class="sr-table-template-move-up">
                                                            <i class="fas fa-arrow-circle-up icon"></i>
                                                        </div>
                                                        <div class="sr-table-template-move-down">
                                                            <i class="fas fa-arrow-circle-down icon"></i>
                                                        </div>
                                                    </div>
                                                    <div class="right-block">
                                                        <span class="sr-table-template-delete">
                                                            <i class=" fas fa-times-circle text-danger icon"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <x-inputs.text-field type="hidden" name="children[(sr-id)][id]"
                                                    label="" />
                                            </td>
                                            <td>
                                                <?php
                                                $field = 'product_id';
                                                ?>

                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <x-Inputs.drop-down name="children[(sr-id)][{{ $field }}]" :label="$false" :list="$empty_list" class="will-select2 child_product_id"/>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="d-flex align-items-start">
                                                            <span id="opener-product_id" class="btn btn-secondary opener-child_product_id flex-shrink-0">Choose</span>
                                                            <input type="hidden" class="choose-child_product_id" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $field = 'make_id';
                                                ?>
                                                <x-Inputs.drop-down id="{{ $field }}-(sr-id)"
                                                    name="children[(sr-id)][{{ $field }}]" :label="$false"
                                                    :list="$maker_list" class="will-required will-select2 make_id" />
                                            </td>
                                            <td>
                                                <?php
                                                $field = 'serial_no';
                                                ?>
                                                <x-Inputs.text-field id="{{ $field }}-(sr-id)"
                                                    name="children[(sr-id)][{{ $field }}]"
                                                    :label="$false"
                                                    class="will-required product_serial_no" />
                                            </td>
                                        </tr>
                                        @php
                                            $children = old('children', $children ?? []);
                                            unset($children['(sr-id)']);
                                        @endphp
                                        @if (isset($children))
                                            @foreach ($children as $k => $child_arr)
                                                <tr class="" sr-id="{{ $child_arr['id'] }}"
                                                    sr-counter="{{ $k }}">
                                                    <td>
                                                        <div class="block">
                                                            <div class="left-block">
                                                                <div class="sr-table-template-move-up">
                                                                    <i class="fas fa-arrow-circle-up icon"></i>
                                                                </div>
                                                                <div class="sr-table-template-move-down">
                                                                    <i class="fas fa-arrow-circle-down icon"></i>
                                                                </div>
                                                            </div>
                                                            <div class="right-block">
                                                                <span class="sr-table-template-delete">
                                                                    <i class=" fas fa-times-circle text-danger icon"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="children[{{ $k }}][id]"
                                                            value="{{ $child_arr['id'] ?? '' }}" />
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $field = 'product_id';
                                                        $value = $child_arr[$field] ?? '';
                                                        ?>

                                                        <div class="row">
                                                            <div class="col-sm-8">
                                                                <x-Inputs.drop-down name="children[{{ $k }}][{{ $field }}]" :label="$false" :list="$empty_list" :value="$value" class="will-select2 child_product_id"/>
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <div class="d-flex align-items-start">
                                                                    <span id="opener-product_id" class="btn btn-secondary opener-child_product_id flex-shrink-0">Choose</span>
                                                                    <input type="hidden" class="choose-child_product_id" value="{{ $value }}"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <?php
                                                        $field = 'make_id';
                                                        $value = $child_arr[$field] ?? '';
                                                        ?>
                                                        <x-Inputs.drop-down id="{{ $field }}-{{ $k }}"
                                                            name="children[{{ $k }}][{{ $field }}]"
                                                            :label="$false" :list="$maker_list" :value="$value"
                                                            class="will-required will-select2 make_id" />
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $field = 'serial_no';
                                                        $value = $child_arr[$field] ?? '';
                                                        ?>
                                                        <x-Inputs.text-field id="{{ $field }}-{{ $k }}"
                                                            name="children[{{ $k }}][{{ $field }}]"
                                                            :label="$false"
                                                            :value="$value"
                                                            class="will-select2 product_serial_no"
                                                            mandatory="true" />
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
            </div>

            <x-Backend.form-common-footer-buttons />
        </div>
    </div>
    </form>

<script>
    $(function() {

        var manual_serial_no_cleave, auto_serial_no_cleave;

        var model_id = '<?= $model->id ?? 0 ?>';

        var serialNoSearchEngine = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: '/product-serial-ajax_get_unlinked_child_list?query=%QUERY',
                wildcard: '%QUERY',
                transform: function(response) {
                    var result = $.map(response['data'], function(item) {
                        return item;
                    });

                    return result;
                }
            },
        });

        function toggle_hierarchy_type() {

            var v = $('#hierarchy_type').val();

            var selector = ".hierarchy_type-block";

            $(selector).hide();
            $(selector).find("input,select").attr("disabled", true);

            if (v)
            {
                v = v.toLowerCase().trim();
                var selector = ".hierarchy_type-block_any, .hierarchy_type-block_" + v;

                if ($(selector).length > 0) {
                    $(selector).show();
                    $(selector).find("input,select").removeAttr("disabled");
                }

                if (v == "parent")
                {
                    $('#no_of_serials').val("1");
                    $('#no_of_serials').attr("readonly", "readonly");
                }
                else
                {
                    $('#no_of_serials').removeAttr("readonly");
                }

                toggle_type();
            }
        }

        function toggle_type() {

            var type = $('input[name="serial_type"]:checked').val();

            var selector = ".type-block";
            $(selector).hide();
            $(selector).find("input,select").attr("disabled", true);
            console.log("hide", selector);

            var selector = ".type-block_any, .type-block_" + type;

            if ($(selector).length > 0) {
                $(selector).show();
                $(selector).find("input,select").removeAttr("disabled");
                console.log("show", selector);
            }

            set_end_serial_no();
        }

        function set_end_serial_no() {
            var type = $('input[name="serial_type"]:checked').val();
            var qty = $('#no_of_serials').val();
            qty = qty ? parseInt(qty) : 0;

            if (qty <= 0)
            {
                return;
            }

            if (type == "manual") {
                var serial_no = manual_serial_no_cleave.getRawValue();
                serial_no = serial_no ? parseInt(serial_no) : 0;

                if (serial_no > 0) {
                    var end_serial_no = serial_no + qty - 1;
                    end_serial_no = end_serial_no.toString().padStart(8, "0");
                    $("#manual_end_serial_no").val(end_serial_no);
                }
            } else if (type == "auto") {
                var serial_no = auto_serial_no_cleave.getRawValue();
                serial_no = serial_no ? parseInt(serial_no) : 0;

                if (serial_no > 0) {
                    var end_serial_no = serial_no + qty - 1;
                    end_serial_no = end_serial_no.toString().padStart(8, "0");
                    $("#auto_end_serial_no").val(end_serial_no);
                }
            }
        }

        function load_product_list(target)
        {
            ajaxGetJson('{{ route("product.ajax_get_list") }}', function(response){

                var html = "";
                var html = "<option value=''>Please Select</option>";

                for (var i in response["data"]) {
                    var k = response["data"][i]["id"];
                    var v = response["data"][i]["name"];

                    var attr = 'value="' + k + '"';

                    if (typeof response["data"][i]["auto_select"] != "undefined") {
                        if (response["data"][i]["auto_select"]) {
                            attr += ' selected="selected"';
                        }
                    }

                    html += '<option ' + attr + '>' + v + "</option>";
                }

                $(target).html(html);

                var v = $(target).attr("data-value");

                if (v) {
                    $(target).val(v).trigger("change");
                }

            });
        }

        function init_child_product_id(_tr, last_id) {

            _tr.find(".choose-child_product_id").browseProductGallery({
                opener: _tr.find(".opener-child_product_id"),
                postfix: "choose-child_product_id",
                onSelect: function(id_list) {
                    _tr.find(".child_product_id").attr("data-value", id_list[0]);
                    load_product_list(_tr.find(".child_product_id"));
                }
            });
        }

        function init_product_serial_no(_tr, last_id) {
            _tr.find('.product_serial_no').typeahead({
                hint: true,
                highlight: true,
                minLength: 1
            }, {
                display : 'name',
                source: serialNoSearchEngine,
                templates: {
                    suggestion: function(data) {
                        return `<div>
                            <span> ${data.display}</span>
                        </div>
                    `;
                    }
                }
            });
        }

        $(".template-table").srTableTemplate({
            brace_type: "(",
            afterRowAdd(_table, last_id, _tr) {
                _tr.find(".will-select2").select2({
                    placeHolder: "Please Select",
                    theme: "bootstrap-5",
                });

                _tr.find(".will-required").attr("required", true);

                load_product_list(_tr.find(".child_product_id"));

                init_child_product_id(_tr);

                init_product_serial_no(_tr, last_id);
            }
        });

        var trs = $(".template-table").find(">tbody>tr").not(".sr-table-template-row");
        trs.each(function(index, _tr) {

            load_product_list($(_tr).find(".child_product_id"));

            init_child_product_id($(_tr));

            init_product_serial_no($(_tr), index);
        })

        auto_serial_no_cleave = new Cleave('#auto_serial_no', {
            blocks: [4, 4],
            delimiter: '-'
        });

        manual_serial_no_cleave = new Cleave('#manual_serial_no', {
            blocks: [4, 4],
            delimiter: '-'
        });

        $("#manual_serial_no, #no_of_serials").blur(set_end_serial_no);
        $("#manual_serial_no, #no_of_serials").on("input", set_end_serial_no);

        $("#choose-product_id").browseProductGallery({
            opener: "#opener-product_id",
            postfix: "choose-product_id",
            onSelect: function(id_list) {
                $("#product_id").attr("data-value", id_list[0]);
                load_product_list($("#product_id"));
            }
        });

        $("input.type").change(toggle_type);

        $("#hierarchy_type").change(toggle_hierarchy_type);

        manufacture_date_time = flatpickr("#manufacture_date_time", {
            enableTime: true,
            time_24hr: true,
            defaultDate: new Date(),
            dateFormat: constants.dateTimeFormat,
            onChange: function(selectedDates, dateStr, instance) {
                const selectedDate = selectedDates[0];
                if (selectedDate) {
                }
            }
        });

        manufacture_date_time.set("maxDate", new Date());

        $.events.execAtLast(function() {

            toggle_hierarchy_type();

            toggle_type();

            load_product_list($("#product_id"));
        });
    });
</script>@endsection
