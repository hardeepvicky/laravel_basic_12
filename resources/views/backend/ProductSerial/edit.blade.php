@extends($layout)

@section('content')
    <?php
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
                            <div class="form-group mb-3">
                                <?php
                                $field = 'serial_no';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                                    value="{{ $model->{$field} }}" placeholder="Enter {{ $label }}"
                                    mandatory="true" />
                            </div>

                            <div class="form-group mb-3">
                                <?php
                                $field = 'product_id';
                                $value = $model->product_id;
                                ?>
                                <div class="d-flex align-items-start">
                                    <span id="choose-product_id" class="btn btn-secondary flex-shrink-0">Choose Product</span>
                                    <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$value" />
                                    <div class="gallery-files-preview m-1">
                                    </div>
                                </div>
                            </div>
                        
                            <div class="form-group mb-3">
                                <?php
                                $field = 'make_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$maker_list" value="{{ $model->{$field} }}" class="select2" mandatory="true" />
                            </div>                            
                            <div class="form-group mb-3">
                                <?php
                                $field = 'is_parent_serial_no';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.checkbox :id="$field" :name="$field" :label="$label" value="{{ $model->{$field} }}" />
                            </div>
                        </div>
                        <div class="col-md-12 col-xxl-12">
                            <div class="type-block type-block_manual">
                                <h4 class="b-b-info">
                                    Child Serial No.
                                </h4>
                                <table class="table table-bordered order-column template-table"
                                    data-sr-table-template-min-row="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width : 50px;">
                                                <span class="sr-table-template-add">
                                                    <i class="fas fa-plus-circle text-success icon"></i>
                                                </span>
                                            </th>
                                            <th style="width:60%">
                                                {{ get_value_from_list($field_labels, 'product_id') }}
                                            </th>                                            
                                            <th style="width:30%">
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
                                                <span class="btn btn-secondary choose-child_product_id flex-shrink-0">Choose Product</span>
                                                <x-Inputs.text-field id="{{ $field }}-(sr-id)" :name="$field" :label="$false" :value="$value" class="child_product_id"/>
                                                <div class="gallery-files-preview m-1">
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $field = 'product_serial_id';
                                                ?>
                                                <x-Inputs.drop-down id="{{ $field }}-(sr-id)" name="children[(sr-id)][{{ $field }}]"
                                                    :label="$false" :list="$empty_list" value="{{ $model->{$field} }}"
                                                    class="will-required product_serial_id" />
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
                                                        <span class="btn btn-secondary choose-child_product_id flex-shrink-0">Choose Product</span>
                                                        <x-Inputs.text-field id="{{ $field }}-{{ $k }}" name="children[{{ $k }}][{{ $field }}]" :label="$false" :value="$value" class="child_product_id"/>
                                                        <div class="gallery-files-preview m-1">
                                                        </div>
                                                    <td>
                                                        <?php
                                                        $field = 'product_serial_id';
                                                        $value = $child_arr['id'] ?? '';
                                                        ?>
                                                        <x-Inputs.drop-down id="{{ $field }}-{{ $k }}"
                                                            name="children[{{ $k }}][{{ $field }}]" :label="$false" :list="$empty_list"
                                                            :value="$value" class="product_serial_id" mandatory="true" />
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
            var model_id = '<?= $model->id ?? 0 ?>';

            function toggle_is_parent_serial_no() {

                var is_parent_serial_no = $('#is_parent_serial_no').prop("checked");
                var selector = "#is_parent_block";

                if (is_parent_serial_no)
                {
                    $(selector).show();
                    $(selector).find("input,select").removeAttr("disabled");
                }
                else
                {
                    $(selector).hide();
                    $(selector).find("input,select").attr("disabled", true);
                }
            }

            function load_child_serial_no(target, product_id, product_serial_id)
            {
                var new_url = "/product-serial-ajax_get_unlinked_list?";
                new_url += "product_id=" + product_id;
                if (product_serial_id)
                {
                    new_url += "&product_serial_id=" + product_serial_id;
                }

                console.log(new_url);
                
                $.get(new_url, function (response) {
                    try {
                        if (typeof response == "string") {
                            response = JSON.parse(response);
                        }
                    } catch (e) {
                        console.error(feature + " Error on get " + new_url,response);
                        return;
                    }

                    var html = "";
                    var html = "<option value=''>Please Select</option>";
                    
                    for (var i in response["data"]) {
                        var k = response["data"][i]["id"];
                        var v = response["data"][i]["name"];

                        var attr = 'value="' + k +'"';

                        if (typeof response["data"][i]["auto_select"] != "undefined")
                        {
                            if (response["data"][i]["auto_select"])
                            {
                                attr += ' selected="selected"';
                            }
                        }

                        html += '<option ' + attr +'>' + v +"</option>";
                    }

                    $(target).html(html);

                    var v = $(target).attr("data-value");

                    if (v)
                    {
                        $(target).val(v);
                    }
                });
            }

            function int_child_product_id(_tr, last_id)
            {
                _tr.find(".child_product_id").browseProductGallery({
                    opener: _tr.find(".choose-child_product_id"),
                    postfix: "choose-child_product_id-" + last_id,
                    onSelect : function(id_list)
                    {
                        var target = _tr.find("select.product_serial_id");

                        if (id_list.length <= 0)
                        {
                            $(target).html("").trigger("change.select2");
                            return;
                        }

                        load_child_serial_no(target, id_list[0], model_id);
                    }
                });
            }

            $(".template-table").srTableTemplate({
                brace_type: "(",
                afterRowAdd(_table, last_id, _tr) {
                    // _tr.find(".will-select2").select2({
                    //     placeHolder: "Please Select",
                    //     theme: "bootstrap-5",
                    // });

                    _tr.find(".will-required").attr("required", true);

                    int_child_product_id(_tr, last_id);
                }
            });

            var trs = $(".template-table").find(">tbody>tr").not(".sr-table-template-row");
            trs.each(function(index, _tr){
                int_child_product_id($(_tr), index);
                
                load_child_serial_no($(_tr).find("select.product_serial_id"), $(_tr).find(".child_product_id").val(), model_id);
            })

            $("#is_parent_serial_no").on("input", toggle_is_parent_serial_no);

            $("#product_id").browseProductGallery({
                opener: "#choose-product_id",
                postfix: "choose-product_id",
            });

            $.events.execAtLast(function() {

                toggle_is_parent_serial_no();
            });
        });
    </script>
@endsection
