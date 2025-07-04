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
                            $field = 'is_link';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.checkbox :id="$field" :name="$field" :label="$label"
                                value="{{ $model->{$field} }}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'parent_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$tree_list"
                                    value="{{ $model->{$field} }}" class="select2" pleaseSelect="Root" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" mandatory="true" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'css_class';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'icon_css_class';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" />
                            </div>
                        </div>                        
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'menu_display_order';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field type="number" :name="$field" :label="$label"
                                    value="{{ $model->{$field} }}" placeholder="Enter {{ $label }}"
                                    mandatory="true" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'show_in_static_menu';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$static_menu_list"
                                    :value="$model->{$field}" class="select2" multiple="true"/>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 is_link-block is_link-block-1">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'route_name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$route_list" value="{{ $model->{$field} }}" class="select2" />
                                <span id="apply_to_related_links" class="btn btn-primary btn-sm">Apply To Related Links</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-8 is_link-block is_link-block-1">
                            <label class="form-label">Related Links</label>
                            <table class="table table-sm table-bordered order-column template-table"
                                data-sr-table-template-min-row="0">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width : 10%;">
                                            <span class="sr-table-template-add">
                                                <i class="fas fa-plus-circle text-success icon"></i>
                                            </span>
                                        </th>
                                        <th style="width : 25%;">Label</th>
                                        <th>Route</th>
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
                                            <x-inputs.text-field type="hidden" name="appMenuRelatedLink[(sr-id)][id]"
                                                class="" />
                                        </td>
                                        <td>
                                            <x-inputs.text-field name="appMenuRelatedLink[(sr-id)][name]" label=""
                                                class="will-require" placeholder="Label" />
                                        </td>
                                        <td>
                                            <x-Inputs.drop-down name="appMenuRelatedLink[(sr-id)][route_name_array][]" label="" :list="$route_list"
                                                value="" class="will-require will-select2 related_link" multiple="multiple" />
                                        </td>
                                    </tr>
                                    @php
                                        $appMenuRelatedLink = old('appMenuRelatedLink', $appMenuRelatedLink ?? []);
                                        unset($appMenuRelatedLink['(sr-id)']);
                                    @endphp
                                    @if (isset($appMenuRelatedLink))
                                        @foreach ($appMenuRelatedLink as $k => $appMenuRelatedLink_arr)
                                            <tr class="" sr-id="{{ $appMenuRelatedLink_arr['id'] }}"
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
                                                    <input type="hidden"
                                                        name="appMenuRelatedLink[{{ $k }}][id]"
                                                        value="{{ $appMenuRelatedLink_arr['id'] ?? '' }}" />
                                                </td>
                                                <td>
                                                    <?php $value = $appMenuRelatedLink_arr['name'] ?? ''; ?>
                                                    <x-inputs.text-field
                                                        name="appMenuRelatedLink[{{ $k }}][name]"
                                                        class="" label="" :value="$value"
                                                        placeholder="Label" mandatory="true"/>
                                                </td>
                                                <td>
                                                    <?php $value = $appMenuRelatedLink_arr['route_name_array'] ?? ''; ?>
                                                    <x-inputs.drop-down
                                                        name="appMenuRelatedLink[{{ $k }}][route_name_array][]"
                                                        class="select2 related_link" label="" :value="$value"
                                                        :list="$route_list" mandatory="true" multiple="multiple" />
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
                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'is_active';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.checkbox :name="$field" :label="$label" value="{{ $model->{$field} }}" />
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


                function show_hide_field() {

                    var is_link = $("#is_link").prop("checked");
                    is_link = is_link ? 1 : 0;

                    var selector = ".is_link-block";
                    $(selector).hide();
                    $(selector).find("input,select").attr("disabled", true);

                    console.log("hide", selector);

                    var selector = ".is_link-block-any, .is_link-block-" + is_link;

                    if ($(selector).length > 0) {
                        console.log("show", selector);
                        $(selector).show();
                        $(selector).find("input,select").removeAttr("disabled");
                    }
                }


                $("#is_link").change(show_hide_field);

                $("#apply_to_related_links").click(function()
                {
                    var text = $("#route_name").find("option:selected").text();

                    var arr = text.split(".");

                    if (typeof arr == "object" && Object.keys(arr).length > 1)
                    {
                        prefix = arr[0];

                        $("select.related_link").each(function(){
                            var new_val_list = [];
                            var option_list = {};
                            $(this).find(">option").each(function(i, ele){
                                option_list[$(ele).text()] = $(ele).val();
                            });

                            $(this).find(">option:selected").each(function(){
                                var r_text = $(this).text();
                                var arr = r_text.split(".");
                                if (typeof arr == "object" && Object.keys(arr).length == 2)
                                {
                                    var postfix = arr[1];

                                    var new_text = prefix + "." + postfix;

                                    if (typeof option_list[new_text] != "undefined")
                                    {
                                        new_val_list.push(option_list[new_text]);
                                    }
                                }
                            });

                            console.log("new values", option_list);

                            if (Object.keys(new_val_list).length > 0)
                            {
                                $(this).val(new_val_list).trigger("change");
                            }
                        })
                    }
                });

                $(".template-table").srTableTemplate({
                    brace_type: "(",
                    afterRowAdd(_table, last_id, _tr) {
                        _tr.find(".will-select2").select2({
                            placeHolder: "Please Select",
                            theme: "bootstrap-5",
                        });

                        _tr.find(".will-require").attr("required", true);
                    }
                });

                show_hide_field();
            });
        })
    </script>
@endsection
