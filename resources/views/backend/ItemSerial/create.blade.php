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
                                $field = 'no_of_serials';
                                $label = "No. Of Serials";
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
                                $field = 'item_id';
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
                                                <span id="opener-item_id" class="btn btn-secondary flex-shrink-0">Choose</span>
                                                <input type="hidden" id="choose-item_id" value="{{  $model->item_id }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                                $field = 'package_qty';
                                $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field layout="horizontal" horizontalSecondColumnLength="3" type="number" :id="$field" :name="$field"
                                :label="$label" :value="$model->{$field}"
                                placeholder="Enter {{ $label }}" mandatory="true" class="validate-int"
                                min="1" max="10000" />

                        </div>
                    </div>
                </div>
            </div>

            @if(request()->has('refer_from'))
            <div class="form-buttons mb-4">
                <button type="submit" class="btn btn-primary" name="save_and_close" value="1">Submit & Close</button>
            </div>
            @else
                <x-Backend.form-common-footer-buttons />
            @endif
        </div>
    </div>
    </form>

<script>
    $(function() {

        var manual_serial_no_cleave, auto_serial_no_cleave;

        var model_id = '<?= $model->id ?? 0 ?>';

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

        function load_item_list(target)
        {
            ajaxGetJson('{{ route("item.ajax_get_list") }}', function(response){

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

        $("#choose-item_id").browseItemGallery({
            opener: "#opener-item_id",
            postfix: "choose-item_id",
            onSelect: function(id_list) {
                $("#item_id").attr("data-value", id_list[0]);
                load_item_list($("#item_id"));
            }
        });

        $("input.type").change(toggle_type);

        $.events.execAtLast(function() {

            toggle_type();

            load_item_list($("#item_id"));
        });
    });
</script>@endsection
