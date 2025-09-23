@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />

    <div class="row">
        <div class="col-xxl-10">
            <div class="card">
                <div class="card-header card-no-border pb-0">
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 col-xxl-6">
                            <?php
                            $field = 'bin_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$bin_list"
                                :value="$model->{$field}" class="select2 " layout="horizontal" mandatory="true" />

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
                                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false"
                                                :list="$product_list" :value="$model->{$field}" class="select2" mandatory="true" />
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="d-flex align-items-start">
                                                <span id="opener-product_id"
                                                    class="btn btn-secondary flex-shrink-0">Choose</span>
                                                <input type="hidden" id="choose-product_id"
                                                    value="{{ $model->product_id }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                            $field = 'make_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$make_list"
                                :value="$model->{$field}" class="select2 " layout="horizontal" mandatory="true" />

                            <?php
                            $field = 'qty';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field type="number" :name="$field" :label="$label" :value="$model->{$field}"
                                layout="horizontal" placeholder="Enter {{ $label }}" mandatory="true"
                                autocomplete="off" />
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

            $("#choose-product_id").browseProductGallery({
                opener: "#opener-product_id",
                postfix: "choose-product_id",
                onSelect: function(id_list) {
                    $("#product_id").attr("data-value", id_list[0]);
                    load_product_list($("#product_id"));
                }
            });
        });
    </script>
@endsection
