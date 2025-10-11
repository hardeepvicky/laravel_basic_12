<x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />
<input type="hidden" name="tab_name" value="{{ $model->tab_name }}" />

<div class="row">
    <div class="col-md-12 col-xxl-6">
        <?php
        $field = 'name';
        $label = get_value_from_list($field_labels, $field, $field);
        ?>
        <x-Inputs.text-field :id="$field" :name="$field" :label="$label" value="{{ $model->{$field} }}"
            placeholder="Enter {{ $label }}" layout="horizontal" mandatory="true" autocomplete="off"  list="name-list"/>

        <datalist id="name-list">
            @foreach($name_list as $name)
                <option>{{ $name }}</option>
            @endforeach
        </datalist>

        <?php
        $field = 'specification';
        $label = get_value_from_list($field_labels, $field, $field);
        ?>
        <x-Inputs.text-field :id="$field" :name="$field" :label="$label" value="{{ $model->{$field} }}"
            placeholder="Enter {{ $label }}" layout="horizontal" autocomplete="off"  list="specification-list"/>

        <datalist id="specification-list">
            @foreach($specification_list as $name)
                <option>{{ $name }}</option>
            @endforeach
        </datalist>

        <?php
        $field = 'size';
        $label = get_value_from_list($field_labels, $field, $field);
        ?>
        <x-Inputs.text-field :id="$field" :name="$field" :label="$label" value="{{ $model->{$field} }}"
            placeholder="Enter {{ $label }}" layout="horizontal" autocomplete="off"  list="size-list"/>

        <datalist id="size-list">
            @foreach($size_list as $name)
                <option>{{ $name }}</option>
            @endforeach
        </datalist>

        <?php
        $field = 'sku';
        $label = get_value_from_list($field_labels, $field, $field);
        ?>
        <x-Inputs.text-field :id="$field" :name="$field" :label="$label" value="{{ $model->{$field} }}"
            placeholder="Enter {{ $label }}" layout="horizontal"  mandatory="true" readonly="readonly" />

        <div class="modal fade" id="modal-category" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title fs-5">Category</h3>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <?php
                        $field = 'root_item_category_id';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                            :list="$root_item_category_list"
                            :value="$model->{$field}"
                            layout="horizontal"
                            class="cascade-drop-down"
                            data-sr-cascade-target="#parent_item_category_id"
                            data-sr-cascade-url="/item-category/ajax_get_list/{v}"
                            mandatory="true"
                            />

                        <?php
                        $field = 'parent_item_category_id';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                @if (isset($label) && $label)
                                    <label class="form-label">
                                        {{ $label }}
                                    </label>
                                @endif
                            </div>
                            <div class="col-sm-9">
                                <div class="d-flex align-items-start">
                                    <span class="btn btn-outline-info summary-action-button open_parent_category_add_form" style="padding: 9px 12px;">
                                        <i class="fa-solid fa-plus"></i>
                                    </span>
                                    <div class="flex-grow-1">
                                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :list="$empty_list"
                                            :value="$model->{$field}"
                                            class="cascade-drop-down"
                                            data-sr-cascade-target="#child_item_category_id"
                                            data-sr-cascade-url="/item-category/ajax_get_list/{v}"
                                            />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        $field = 'child_item_category_id';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                @if (isset($label) && $label)
                                    <label class="form-label">
                                        {{ $label }}
                                    </label>
                                @endif
                            </div>
                            <div class="col-sm-9">
                                <div class="d-flex align-items-start">
                                    <span class="btn btn-outline-info summary-action-button open_child_category_add_form" style="padding: 9px 12px;">
                                        <i class="fa-solid fa-plus"></i>
                                    </span>
                                    <div class="flex-grow-1">
                                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :list="$empty_list"
                                            :value="$model->{$field}" class="" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary submit">Done</button>
                    </div>
                </div>
            </div>
        </div>

        <?php
        $field = 'child_item_category_id_main';
        $label = get_value_from_list($field_labels, $field, $field);
        ?>
        <div class="row mb-3">
            <div class="col-sm-3">
                Cat.
            </div>
            <div class="col-sm-9">
                <div class="d-flex align-items-start">
                    <span id="modal-category-opener" class="btn btn-outline-info summary-action-button" style="padding: 9px 12px;">
                        <i class="fa-solid fa-eye"></i>
                    </span>
                    <div class="flex-grow-1">
                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :list="$item_category_list"
                            :value="$model->{$field}" class="select2" />
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-sm-3">
                Dept
            </div>
            <div class="col-sm-9">
                <table class="table table-bordered order-column template-table" data-sr-table-template-min-row="0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width : 8%;">
                                <span class="sr-table-template-add">
                                    <i class="fas fa-plus-circle text-success icon"></i>
                                </span>
                            </th>
                            <th style="width:30%">
                                {{ get_value_from_list($item_sub_department_field_labels, 'dept_id') }}
                            </th>
                            <th style="width:60%">
                                {{ get_value_from_list($item_sub_department_field_labels, 'sub_dept_id') }}
                            </th>
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
                                <x-inputs.text-field type="hidden" name="item_sub_departments[(sr-id)][id]" label="" />
                            </td>
                            <td>
                                <?php
                                $field = 'dept_id';
                                ?>
                                <x-Inputs.drop-down name="item_sub_departments[(sr-id)][{{ $field }}]" :label="$false"
                                    :list="$dept_list"
                                    class="will-select2 will-required dept_id"
                                    data-sr-cascade-target="#sub_dept_id-(sr-id)"
                                    data-sr-cascade-url="/sub-department/ajax_get_list/{v}"
                                    />
                            </td>
                            <td>
                                <?php
                                $field = 'sub_dept_id';
                                ?>
                                <x-Inputs.drop-down id="{{ $field }}-(sr-id)"
                                    name="item_sub_departments[(sr-id)][{{ $field }}][]"
                                    :label="$false"
                                    :list="$empty_list"
                                    class="will-required will-select2 sub_dept_id"
                                    multiple="true"
                                    />
                            </td>
                        </tr>
                        @php
                            $item_sub_departments = old('item_sub_departments', $model->itemSubDepartment?->toArray() ?? []);
                            unset($item_sub_departments['(sr-id)']);
                        @endphp
                        @if (isset($item_sub_departments))
                            @foreach ($item_sub_departments as $k => $child_arr)
                                <tr class="" sr-id="{{ $child_arr['id'] }}"
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
                                        <input type="hidden" name="item_sub_departments[{{ $k }}][id]" value="{{ $child_arr['id'] ?? '' }}" />
                                    </td>
                                    <td>
                                        <?php
                                        $field = 'dept_id';
                                        $value = $child_arr[$field] ?? '';
                                        ?>
                                        <x-Inputs.drop-down name="item_sub_departments[{{ $k }}][{{ $field }}]" :label="$false"
                                            :list="$dept_list"
                                            :value="$value"
                                            class="select2 dept_id"
                                            data-sr-cascade-target="#sub_dept_id-{{ $k }}"
                                            data-sr-cascade-url="/sub-department/ajax_get_list/{v}"
                                            required="true"
                                            />
                                    </td>
                                    <td>
                                        <?php
                                        $field = 'sub_dept_id';
                                        $value = $child_arr[$field] ?? '';
                                        ?>
                                        <x-Inputs.drop-down id="{{ $field }}-{{ $k }}"
                                            name="item_sub_departments[{{ $k }}][{{ $field }}][]"
                                            :label="$false"
                                            :list="$empty_list"
                                            :value="$value"
                                            class="select2 sub_dept_id"
                                            required="true"
                                            multiple="true"
                                            />
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-sm-3">
                Make
            </div>
            <div class="col-sm-9">
                <table class="table table-bordered order-column template-table" data-sr-table-template-min-row="0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width : 8%;">
                                <span class="sr-table-template-add">
                                    <i class="fas fa-plus-circle text-success icon"></i>
                                </span>
                            </th>
                            <th style="width:30%">
                                {{ get_value_from_list($item_brand_parts_field_labels, 'make_id') }}
                            </th>
                            <th style="width:60%">
                                {{ get_value_from_list($item_brand_parts_field_labels, 'part_no') }}
                            </th>
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
                                <x-inputs.text-field type="hidden" name="item_brand_parts[(sr-id)][id]" label="" />
                            </td>
                            <td>
                                <?php
                                $field = 'make_id';
                                ?>
                                <x-Inputs.drop-down name="item_brand_parts[(sr-id)][{{ $field }}]" :label="$false"
                                    :list="$empty_list"
                                    class="will-select2 will-required make_id"
                                    />
                            </td>
                            <td>
                                <?php
                                $field = 'part_no';
                                ?>
                                <x-Inputs.text-field name="item_brand_parts[(sr-id)][{{ $field }}]" :label="$false"
                                    class="part_no"
                                />
                            </td>
                        </tr>
                        @php
                            $item_brand_parts = old('item_brand_parts', $model->itemBrandPart?->toArray() ?? []);
                            unset($item_brand_parts['(sr-id)']);
                        @endphp
                        @if (isset($item_brand_parts))
                            @foreach ($item_brand_parts as $k => $child_arr)
                                <tr class="" sr-id="{{ $child_arr['id'] }}"
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
                                        <input type="hidden" name="item_brand_parts[{{ $k }}][id]" value="{{ $child_arr['id'] ?? '' }}" />
                                    </td>
                                    <td>
                                        <?php
                                        $field = 'make_id';
                                        $value = $child_arr[$field] ?? '';
                                        ?>
                                        <x-Inputs.drop-down name="item_brand_parts[{{ $k }}][{{ $field }}]" :label="$false"
                                            :list="$empty_list"
                                            :value="$value"
                                            class="will-select2 will-required make_id"
                                            required="true"
                                            />
                                    </td>
                                    <td>
                                        <?php
                                        $field = 'part_no';
                                        $value = $child_arr[$field] ?? '';
                                        ?>
                                        <x-Inputs.text-field name="item_brand_parts[{{ $k }}][{{ $field }}]" :label="$false"
                                            :value="$value"
                                            class="part_no"
                                            />
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <?php
        $field = 'images';
        $label = get_value_from_list($field_labels, $field, $field);
        $value = implode(',', $model->images?->pluck('id')->toArray());
        ?>
        <div class="row mb-3">
            <div class="col-sm-3">
                <label class="form-label">
                    {{ $label }}
                </label>
            </div>
            <div class="col-sm-9">
                <span id="choose-images" class="btn btn-secondary">
                    Choose Images
                </span>
                <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                    :value="$value" multiple="multiple" />
            </div>
        </div>
        <div class="row mb-3" id="photo-capture-block">
            <div class="col-sm-3">
                <label class="form-label">
                    {{ $label }}
                </label>
            </div>
            <div class="col-sm-3">
                <video id="video" width="320" height="240" autoplay></video>
                <span id="capture" class="btn btn-secondary">
                    <i class="fa-solid fa-camera"></i>
                    Capture Photo
                </span>
                <span id="upload" class="btn btn-info">
                    Upload Photo
                </span>
            </div>
            <div class="col-sm-3">
                <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
                <img id="preview" src="" alt="Captured image" style="display:none; border: 1px solid #ccc;"/>
            </div>
        </div>
    </div>
</div>

<x-Backend.form-common-footer-buttons buttons="next" />
</form>

<script>
    $(function() {
        var screen_name = '<?= $screen_name ?>';
        var sku_pattern = '<?= $sku_pattern ?>';
        var selected_department_id = '<?= $selected_department_ids[0] ?>';
        var color, category;

        navigator.getUserMedia = navigator.getUserMedia ||
                         navigator.webkitGetUserMedia ||
                         navigator.mozGetUserMedia;

        if (navigator.getUserMedia)
        {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function (stream) {
                    $('#video')[0].srcObject = stream;
                })
                .catch(function (err) {
                    $.events.onUserWarning("Camera access denied: " + err);
                });

            $('#capture').on('click', function () {
                const canvas = $('#canvas')[0];
                const context = canvas.getContext('2d');
                context.drawImage($('#video')[0], 0, 0, canvas.width, canvas.height);

                const dataURL = canvas.toDataURL('image/png');
                $('#preview').attr('src', dataURL).show();
            });
        }
        else
        {
            $.events.onUserWarning("Camera not supported");
            $("#photo-capture-block").hide();
        }


        function create_sku()
        {
            var sku = sku_pattern;

            if (category)
            {
                console.log(category);
                sku = sku.replace("{category}", category['short_name']);
            }
            else
            {
                sku = sku.replace("{category}", "");
            }

            if (color)
            {
                console.log(color);
                sku = sku.replace("{color}", color['short_name']);
            }
            else
            {
                sku = sku.replace("{color}", "");
            }

            var size = $("#size").val();

            if (size)
            {
                sku = sku.replace("{size}", size);
            }
            else
            {
                sku = sku.replace("{size}", "");
            }

            var specification = $("#specification").val();

            if (specification)
            {
                sku = sku.replace("{specification}", specification);
            }
            else
            {
                sku = sku.replace("{specification}", "");
            }

            sku = sku.toLowerCase();

            var name = $("#name").val();

            if (name)
            {
                name = name.charAt(0).toUpperCase() + name.slice(1);
                sku = sku.replace("{name}", name);
            }
            else
            {
                sku = sku.replace("{name}", "");
            }

            //replace char except hyphine and underscore and remove hyphine from start and end of string
            sku = sku.replace(/[^a-zA-Z0-9-_.*/]/g, '-').replace(/-+/g, '-').replace(/^-+|-+$/g, "");

            $("#sku").val(sku);

            return sku;
        }

        $("#root_item_category_id").change(function(){
            var v = $(this).val();

            if (v)
            {
                ajaxGetJson("/item-brand/ajax_get_list/" + v, function(response) {
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

                    var target = ".make_id";

                    $(target).html(html);

                    $(target).each(function(){
                        var v = $(this).attr("data-value");
                        $(this).val(v).trigger("change");
                    });
                });
            }
            else
            {
                create_sku();
            }
        });


        $("#parent_item_category_id").change(function(){
            var v = $(this).val();
            category = undefined;

            if (v)
            {
                ajaxGetJson("/item-category/ajax_get/" + v, function(response) {
                    category = response['data'];
                    create_sku();
                });
            }
            else
            {
                create_sku();
            }
        }).trigger("change", {pageLoad : true});

        $("#child_item_category_id_main").change(function(e, opt){
            var v = $(this).val();
            category = undefined;

            if (v && (typeof opt == "undefined" || typeof opt.submit == "undefined"))
            {
                ajaxGetJson("/item-category/ajax_get_with_parent/" + v, function(response) {
                    console.log(response['data']);

                    if (typeof response['data']['parent_category_id'] != "undefined")
                    {
                        $("#parent_item_category_id").attr("data-value", response['data']['parent_category_id']);
                    }

                    if (typeof response['data']['child_category_id'] != "undefined")
                    {
                        $("#child_item_category_id").attr("data-value", response['data']['child_category_id']);
                    }

                    if (typeof response['data']['root_category_id'] != "undefined")
                    {
                        $("#root_item_category_id").val(response['data']['root_category_id']).trigger("change", {pageLoad : true, eventBubble : true});
                    }
                });
            }
            else
            {
                create_sku();
            }
        }).trigger("change", {pageLoad : true});

        $("#modal-category .submit").click(function()
        {
            var v = $("#child_item_category_id").val();
            if (v)
            {
                $("#child_item_category_id_main").val(v).trigger("change", {submit : true});
                $("#modal-category").modal("hide");
                return;
            }

            var v = $("#parent_item_category_id").val();
            if (v)
            {
                $("#child_item_category_id_main").val(v).trigger("change", {submit : true});
                $("#modal-category").modal("hide");
                return;
            }

            var v = $("#root_item_category_id").val();
            if (v)
            {
                $("#child_item_category_id_main").val(v).trigger("change", {submit : true});
                $("#modal-category").modal("hide");
                return;
            }
        });

        $("#images").browseGallery({
            opener: "#choose-images",
            postfix: "choose-images",
            type: "image",
            index_table_id : "document_file_images"
        });

        $(".open_parent_category_add_form").click(function(){
            var cat_id = $("#root_item_category_id").val();

            if (!cat_id)
            {
                $.events.onUserError("Please Select Root Category");
                return false;
            }

            $.loader.setInfo("Redirecting..").show();

            window.location.href = "/item-category/create?parent_id=" + cat_id;
        });

        $(".open_child_category_add_form").click(function(){
            var cat_id = $("#parent_item_category_id").val();

            if (!cat_id)
            {
                $.events.onUserError("Please Select Parent Category");
                return false;
            }

            $.loader.setInfo("Redirecting..").show();

            window.location.href = "/item-category/create?parent_id=" + cat_id;
        });

        function init_child_dept(_tr, last_id) {

            _tr.find(".dept_id").cascade({
                beforeGet: function (src, url) {
                    $.loader.init().setInfo("Fetching...").show();
                    return url;
                },
                afterGet: function (src, dest, response) {
                    $.loader.init().hide();
                    return response;
                },
                beforeValueSet: function (src, dest, val) {
                    var v = val.split(",");
                    return v;
                },
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

                init_child_dept(_tr);

                _tr.find(".dept_id").val(selected_department_id).trigger("change", {pageLoad : true});
            }
        });

        var trs = $(".template-table").find(">tbody>tr").not(".sr-table-template-row");
        trs.each(function(){

            init_child_dept($(this));
        });


        $("#modal-category-opener").click(function(){
            $("#modal-category").modal("show");
        });

        $("#name, #size, #specification").autocomplete().on('pick.bs.autocomplete', (e) => {
            create_sku();
        });

        $.events.execAtLast(function() {
            $("#name, #size, #specification").on("input keyup", create_sku);

            $("#root_item_category_id").trigger("change", {pageLoad : true});

            $(".dept_id").trigger("change", {pageLoad : true});
        });
    })
</script>