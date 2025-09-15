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
                            $field = 'ticket_no';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                layout="horizontal"
                                placeholder="Enter {{ $label }}" disabled="true"/>

                            <?php
                            $field = 'product_serial_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    @if (isset($label) && $label)
                                        <label class="form-label">
                                            {{ $label }}
                                            <span class="mandatory">*</span>
                                        </label>
                                    @endif
                                </div>
                                <div class="col-sm-9">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-grow-1">
                                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$false" :list="$product_serial_list"
                                                :value="$model->{$field}"
                                                class="select2"
                                                mandatory="true" />
                                        </div>
                                        <div>
                                            <a target="_blank" href="{{ route('product-serial.create',['refer_from' => 'product_serial']) }}" class="btn btn-outline-secondary">
                                                Add
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                                $field = 'contact_type';
                                $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                :list="$contact_type_list"
                                :value="$model->{$field}"
                                layout="horizontal"
                                horizontalSecondColumnLength="3"
                                class="select2"
                                mandatory="true" />

                            <?php
                                $field = 'contact_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                            ?>

                            <div class="row mb-3 contact">
                                <div class="col-sm-3">
                                    @if (isset($label) && $label)
                                        <label class="form-label">
                                            {{ $label }}
                                            <span class="mandatory">*</span>
                                        </label>
                                    @endif
                                </div>
                                <div class="col-sm-9">
                                    <div class="d-flex align-items-start">
                                        <span class="btn btn-outline-info summary-action-button open_contact_modal" style="padding: 9px 12px;">
                                            <i class="fa-solid fa-user"></i>
                                        </span>
                                        <div class="flex-grow-1">
                                            <select id="{{ $field }}" name="{{ $field }}" class="form-select select2" required="true">
                                                <option value="">Please Select</option>
                                                @foreach ($contact_list as $k => $t)
                                                    @php
                                                        $attr = $k == $model->{$field} ? 'selected="selected"' : '';
                                                    @endphp
                                                    <option value="{{ $k }}" {!! $attr !!}>
                                                        {{ $t }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @error($field)
                                                <div class="input-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                                $field = 'export_contact_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                            ?>

                            <div class="row mb-3 export_contact">
                                <div class="col-sm-3">
                                    @if (isset($label) && $label)
                                        <label class="form-label">
                                            {{ $label }}
                                            <span class="mandatory">*</span>
                                        </label>
                                    @endif
                                </div>
                                <div class="col-sm-9">
                                    <div class="d-flex align-items-start">
                                        <span class="btn btn-outline-info summary-action-button open_export_contact_modal" style="padding: 9px 12px;">
                                            <i class="fa-solid fa-user"></i>
                                        </span>
                                        <div class="flex-grow-1">
                                            <select id="{{ $field }}" name="{{ $field }}" class="form-select select2" required="true">
                                                <option value="">Please Select</option>
                                                @foreach ($export_contact_list as $k => $t)
                                                    @php
                                                        $attr = $k == $model->{$field} ? 'selected="selected"' : '';
                                                    @endphp
                                                    <option value="{{ $k }}" {!! $attr !!}>
                                                        {{ $t }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @error($field)
                                                <div class="input-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                            $field = 'charge_fee';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field layout="horizontal" :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}"                                 
                                class="validate-float"
                                placeholder="Enter {{ $label }}" />

                            <?php
                            $field = 'remarks';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-area layout="horizontal" :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}" 
                                rows="3"
                                placeholder="Enter {{ $label }}" />

                            <div class="row mb-4">
                                <div class="col-sm-3"></div>
                                <div class="col-sm-3">
                                    <?php
                                    $field = 'is_fixed';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.checkbox :id="$field" :name="$field" :label="$label"
                                        value="{{ $model->{$field} }}" />
                                </div>
                            </div>
                        </div>

                        <table class="table table-bordered order-column template-table mb-4"
                            data-sr-table-template-min-row="1">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width : 50px;">
                                        <span class="sr-table-template-add">
                                            <i class="fas fa-plus-circle text-success icon"></i>
                                        </span>
                                    </th>
                                    <th style="width:10%">
                                        {{ get_value_from_list($service_issue_field_labels, 'issue_type') }}
                                    </th>
                                    <th style="width:30%">
                                        {{ get_value_from_list($service_issue_field_labels, 'issue') }}
                                    </th>
                                    <th style="width:10%">
                                        {{ get_value_from_list($service_issue_field_labels, 'solution_type') }}
                                    </th>
                                    <th style="width:40%">
                                        {{ get_value_from_list($service_issue_field_labels, 'solution') }}
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
                                        <x-inputs.text-field type="hidden" name="service_issues[(sr-id)][id]" label="" />
                                    </td>
                                    <td>
                                        <?php
                                        $field = 'issue_type';
                                        ?>
                                        <x-Inputs.drop-down name="service_issues[(sr-id)][{{ $field }}]" :label="$false" :list="$issue_type_list"
                                            class="will-required issue_type will-select2"
                                            />
                                    </td>
                                    <td>
                                        <?php
                                        $field = 'issue_id';
                                        ?>
                                        <div class="issue_id-block">
                                            <x-Inputs.drop-down name="service_issues[(sr-id)][{{ $field }}]"  :label="$false" :list="$empty_list"
                                                class="will-select2 issue_id"
                                                />
                                        </div>

                                        <?php
                                        $field = 'issue';
                                        ?>
                                        <x-Inputs.text-area name="service_issues[(sr-id)][{{ $field }}]"  :label="$false" class="issue"/>
                                    </td>
                                    <td>
                                        <?php
                                        $field = 'solution_type';
                                        ?>
                                        <x-Inputs.drop-down name="service_issues[(sr-id)][{{ $field }}]"   :label="$false" :list="$issue_type_list"
                                            class="will-select2 solution_type"
                                            />
                                    </td>
                                    <td>
                                        <?php
                                        $field = 'issue_solution_id';
                                        ?>
                                        <div class="issue_solution_id-block">
                                            <x-Inputs.drop-down name="service_issues[(sr-id)][{{ $field }}]"  :label="$false" :list="$empty_list"
                                                class="will-select2 issue_solution_id"
                                                />
                                        </div>

                                        <?php
                                        $field = 'solution';
                                        ?>
                                        <x-Inputs.text-area name="service_issues[(sr-id)][{{ $field }}]"   :label="$false" class="solution"/>
                                    </td>
                                </tr>
                                @php
                                    $service_issues = old('service_issues', $model->serviceIssue?->toArray() ?? []);
                                    unset($service_issues['(sr-id)']);
                                @endphp
                                @if (isset($service_issues))
                                    @foreach ($service_issues as $k => $child_arr)
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
                                                <input type="hidden" name="service_issues[{{ $k }}][id]"
                                                    value="{{ $child_arr['id'] ?? '' }}" />
                                            </td>
                                            <td>
                                                <?php
                                                $field = 'issue_type';
                                                $value = $child_arr[$field] ?? "";
                                                ?>
                                                <x-Inputs.drop-down name="service_issues[{{ $k }}][{{ $field }}]" :label="$false" :list="$issue_type_list" :value="$value"
                                                    class="will-required issue_type select2"
                                                    />
                                            </td>
                                            <td>
                                                <?php
                                                $field = 'issue_id';
                                                $value = $child_arr[$field] ?? "";
                                                ?>
                                                <div class="issue_id-block">
                                                    <x-Inputs.drop-down name="service_issues[{{ $k }}][{{ $field }}]"  :label="$false" :list="$empty_list" :value="$value"
                                                        class="select2 issue_id"
                                                        />
                                                </div>

                                                <?php
                                                $field = 'issue';
                                                $value = $child_arr[$field] ?? "";
                                                ?>
                                                <x-Inputs.text-area name="service_issues[{{ $k }}][{{ $field }}]"  :label="$false" :value="$value" class="issue"/>
                                            </td>

                                            <td>
                                                <?php
                                                $field = 'solution_type';
                                                $value = $child_arr[$field] ?? "";
                                                ?>
                                                <x-Inputs.drop-down name="service_issues[{{ $k }}][{{ $field }}]"   :label="$false" :list="$issue_type_list" :value="$value"
                                                    class="select2 solution_type"
                                                    />
                                            </td>
                                            <td>
                                                <?php
                                                $field = 'issue_solution_id';
                                                $value = $child_arr[$field] ?? "";
                                                ?>
                                                <div class="issue_solution_id-block">
                                                    <x-Inputs.drop-down name="service_issues[{{ $k }}][{{ $field }}]"  :label="$false" :list="$empty_list" :value="$value"
                                                        class="select2 issue_solution_id"
                                                        />
                                                </div>

                                                <?php
                                                $field = 'solution';
                                                $value = $child_arr[$field] ?? "";
                                                ?>
                                                <x-Inputs.text-area name="service_issues[{{ $k }}][{{ $field }}]"   :label="$false" :value="$value" class="solution"/>
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

            <x-Backend.form-common-footer-buttons />
        </div>
    </div>
</form>


<script>
    $(function() {
        $(".template-table").srTableTemplate({
            brace_type: "(",
            afterRowAdd(_table, last_id, _tr) {
                _tr.find(".will-select2").select2({
                    placeHolder: "Please Select",
                    theme: "bootstrap-5",
                });

                _tr.find(".will-required").attr("required", true);
            }
        });

        $(document).on("change", "#contact_type", function(){
            var v = $(this).val();

            $(".contact, .export_contact").find("input,select").removeAttr("required");
            $(".contact, .export_contact").hide();

            if (v == "sale")
            {
                $(".contact").find("input,select").attr("required", true);
                $(".contact").show();
            }

            if (v == "export")
            {
                $(".export_contact").find("input,select").attr("required", true);
                $(".export_contact").show();
            }
        });


        $(document).on("change", ".issue_type", function(){
            var v = $(this).val();
            var _tr = $(this).closest("tr");

            _tr.find(".issue, .issue_id-block").addClass("hidden");

            if (v == "regular")
            {
                _tr.find(".issue_id-block").removeClass("hidden");
            }
            else
            {
                _tr.find(".issue").removeClass("hidden");
            }
        });

        $(document).on("change", ".solution_type", function(){
            var v = $(this).val();
            var _tr = $(this).closest("tr");

            _tr.find(".solution, .issue_solution_id-block").addClass("hidden");

            if (v == "regular")
            {
                _tr.find(".issue_solution_id-block").removeClass("hidden");
            }
            else
            {
                _tr.find(".solution").removeClass("hidden");
            }
        });

        $(document).on("change", ".issue_id", function(){
            var _tr = $(this).closest("tr");

            load_solution_list(_tr);
        });

        function load_solution_list(_tr)
        {
            var issue_id = _tr.find(".issue_id").val();

            ajaxGetJson("/product-issue-ajax_get_solution_list?=issue_id=" + issue_id, function(response){
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

                var target = _tr.find(".issue_solution_id");

                $(target).html(html);

                var v = $(target).attr("data-value");

                if (v) {
                    $(target).val(v).trigger("change");
                }
            });
        }

        function load_issue_list()
        {
            var product_serial_id = $("#product_serial_id").val();

            ajaxGetJson("/product-issue-ajax_get_list?=product_serial_id=" + product_serial_id, function(response){
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

                var target = ".issue_id";

                $(target).html(html);

                $(target).each(function(){
                    var v = $(this).attr("data-value");

                    if (v) {
                        $(this).val(v).trigger("change");
                    }
                });
            });
        }

        $("#product_serial_id").change(function(){
            load_issue_list()
        });

        function load_product_serial_list()
        {
            ajaxGetJson("/product-serial-ajax_get_list", function(response){
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

                var target = "#product_serial_id";

                $(target).html(html);

                var v = $(target).attr("data-value");

                if (v) {
                    $(target).val(v).trigger("change");
                }
            });
        }

        $(".open_contact_modal").browseContact({
            selectedValues : 0,
            multiple : 0,
            onSelect : function(id_list)
            {
                var obj = id_list[0];

                $("#contact_id").val(obj.id).trigger("change");
            }
        });

        $(".open_export_contact_modal").browseContact({
            baseUrl : '/export-contact-chooser',
            selectedValues : 0,
            multiple : 0,
            onSelect : function(id_list)
            {
                var obj = id_list[0];

                $("#export_contact_id").val(obj.id).trigger("change");
            }
        });

        $(window).on('storage', function(e) {
            const event = e.originalEvent; // unwrap native event

            if (event.key == "last_product_serial_insert_id")
            {
                let id = event.newValue;

                $("#product_serial_id").attr("data-value", id);

                load_product_serial_list();
            }

            console.log("StorageKey:", event.key, " Old Value : ", event.oldValue, " New Value : ", event.newValue);
        });

        $.events.execAtLast(function() {
            $("#contact_type").trigger("change", {pageLoad : true});

            $(".issue_type, .solution_type").trigger("change", {pageLoad : true});

            $("#product_serial_id").trigger("change", {pageLoad : true});
        });
    })
</script>
@endsection
