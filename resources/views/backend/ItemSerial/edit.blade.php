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
                                $field = 'serial_no';
                                $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field id="manual_serial_no"  :name="$field"
                                :label="$label" :value="$model->{$field}"
                                placeholder="Enter {{ $label }}"
                                layout="horizontal"
                                horizontalSecondColumnLength="3"
                                mandatory="true"
                            />

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
                                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :list="$empty_list" :value="$model->{$field}"
                                                class="select2 cascade-drop-down"
                                                data-sr-cascade-target="#item_brand_id"
                                                data-sr-cascade-url="/item/ajax_get_brand_list/{v}"
                                                mandatory="true"
                                            />
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
                                $field = 'item_brand_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                :list="$empty_list"
                                :value="$model->{$field}"
                                layout="horizontal"
                                class="select2"
                                mandatory="true"
                                />

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

            <x-Backend.form-common-footer-buttons />
        </div>
    </div>
    </form>

<script>
    $(function() {

        var manual_serial_no_cleave, auto_serial_no_cleave;

        var model_id = '<?= $model->id ?? 0 ?>';

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
                    $(target).val(v).trigger("change", {
                        pageLoad: true
                    });
                }
            });
        }

        manual_serial_no_cleave = new Cleave('#manual_serial_no', {
            blocks: [4, 4],
            delimiter: '-'
        });

        $("#choose-item_id").browseItemGallery({
            opener: "#opener-item_id",
            postfix: "choose-item_id",
            onSelect: function(id_list) {
                $("#item_id").attr("data-value", id_list[0]);
                load_item_list($("#item_id"));
            }
        });

        $.events.execAtLast(function() {

            load_item_list($("#item_id"));
        });
    });
</script>@endsection
