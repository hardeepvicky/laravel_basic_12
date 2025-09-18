@extends($layout)

@section('content')
    <?php
    use App\Models\Lead;
    use App\Models\Customer;

    $page_header_links = [];

    if ($model->id)
    {
        $page_header_links[] = ['title' => 'History', 'url' => route($routePrefix . '.history', [$model->id])];
    }

    $page_header_links[] = ['title' => 'List', 'url' => route($routePrefix . '.index')];
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
                            $field = 'mobile';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <label class="form-label">
                                        {{ $label }}
                                    </label>
                                </div>
                                <div class="col-sm-9">
                                    <x-Inputs.group-text-field :id="$field" :name="$field" :label="$false" value="{{ $model->{$field} }}"
                                        placeholder="Enter {{ $label }}" 
                                        layout="horizontal"
                                        placeholder="Enter {{ $label }}" 
                                        autocomplete="off"
                                        secondaryTextPosition="first"
                                        secondaryText='<span class="input-group-text">+91</span>'
                                        mandatory="true" 
                                        />
                                </div>
                            </div>
                                       
                            <?php
                            $field = 'mobile2';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <label class="form-label">
                                        {{ $label }}
                                    </label>
                                </div>
                                <div class="col-sm-9">
                                    <x-Inputs.group-text-field :id="$field" :name="$field" :label="$false" value="{{ $model->{$field} }}"
                                        placeholder="Enter {{ $label }}" 
                                        layout="horizontal"
                                        placeholder="Enter {{ $label }}" 
                                        autocomplete="off"
                                        secondaryTextPosition="first"
                                        secondaryText='<span class="input-group-text">+91</span>'
                                        mandatory="true" 
                                        />
                                </div>
                            </div>
                            
                            <?php
                            $field = 'name';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label"
                                :value="$model->{$field}"
                                layout="horizontal"
                                placeholder="Enter {{ $label }}"
                                />
                            
                            <?php
                            $field = 'state_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                :list="$state_list"
                                :value="$model->{$field}"
                                layout="horizontal"
                                class="select2"
                                data-sr-cascade-target="#city_id"
                                data-sr-cascade-url="/city/ajax_get_list_with_district/{v}"
                                />

                            <?php
                            $field = 'city_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                :list="$district_city_list"
                                :value="$model->{$field}"
                                layout="horizontal"
                                class="select2"
                                />

                            <?php
                            $field = 'email';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label"
                                :value="$model->{$field}"
                                layout="horizontal"
                                class="validate-email"
                                placeholder="Enter {{ $label }}"
                            />

                            <?php
                            $field = 'assign_to_user_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                :list="$user_list" :value="$model->{$field}"
                                layout="horizontal"
                                class="select2"
                                />

                            <?php
                            $field = 'status_type';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                :list="$status_type_list_for_form" :value="$model->{$field}"
                                layout="horizontal"
                                class="select2"
                                mandatory="true" />

                            <?php
                            $field = 'deal_type';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                :list="$deal_type_list" :value="$model->{$field}"
                                layout="horizontal"
                                class="select2"
                                />


                            <?php
                            $field = 'dealer_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                :list="$dealer_list" :value="$model->{$field}"
                                layout="horizontal"
                                class="select2"
                            />

                            <?php
                            $field = 'company_name';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label"
                                :value="$model->{$field}"
                                layout="horizontal"
                                class=""
                                placeholder="Enter {{ $label }}" />

                            <?php
                            $field = 'brand_name';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label"
                                :value="$model->{$field}"
                                layout="horizontal"
                                class=""
                                placeholder="Enter {{ $label }}" />

                            <?php
                            $field = 'company_gst_no';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label"
                                :value="$model->{$field}"
                                layout="horizontal"
                                class=""
                                placeholder="Enter {{ $label }}" />

                            <?php
                            $field = 'remarks';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-area :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}"
                                rows="3"
                                layout="horizontal"
                                placeholder="Enter {{ $label }}"
                                />

                            <?php
                            $field = 'leadFile';
                            $label = get_value_from_list($field_labels, $field, $field);
                            $value = implode(',', $model->lead?->leadFile?->pluck('document_file_id')->toArray() ?? []);
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
                                    <div class="form-group">
                                        <x-Inputs.text-field :id="$field" :name="$field" :label="$false" :value="$value" multiple="multiple" />
                                        <span id="files-choose" class="btn btn-secondary">Choose Files</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h4 class="b-b-info">
                                        Interested Products
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
                                                <th style="width:70%">
                                                    {{ get_value_from_list($product_field_labels, 'product_id') }}
                                                </th>
                                                <th style="width:20%">
                                                    {{ get_value_from_list($product_field_labels, 'qty') }}
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
                                                    <x-inputs.text-field type="hidden" name="products[(sr-id)][id]"
                                                        label="" />
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'product_id';
                                                    ?>
                                                    <x-inputs.drop-down name="products[(sr-id)][{{ $field }}]"
                                                        label="" :list="$product_list" class="will-required will-select2"
                                                        />
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'qty';
                                                    ?>
                                                    <x-inputs.text-field name="products[(sr-id)][{{ $field }}]"
                                                        label="" class="will-required validate-float"/>
                                                </td>
                                            </tr>
                                            @php
                                                $products = old('products', $products ?? []);
                                                unset($products['(sr-id)']);
                                            @endphp
                                            @if (isset($products))
                                                @foreach ($products as $k => $child_arr)
                                                    <tr class="" sr-id="{{ $child_arr['id'] ?? $k }}"
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
                                                                        <i
                                                                            class=" fas fa-times-circle text-danger icon"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="products[{{ $k }}][id]"
                                                                value="{{ $child_arr['id'] ?? '' }}" />
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $field = 'product_id';
                                                            $value = $child_arr[$field] ?? '';
                                                            ?>
                                                            <x-inputs.drop-down name="products[{{ $k }}][{{ $field }}]"
                                                                label="" :list="$product_list" :value="$value" class="will-required will-select2"
                                                                />
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $field = 'qty';
                                                            $value = $child_arr[$field] ?? '';
                                                            ?>
                                                            <x-inputs.text-field name="products[{{ $k }}][{{ $field }}]"
                                                                label="" :value="$value" class="will-required validate-float"/>
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

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h4 class="b-b-info">
                                        Addresses
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
                                                <th style="width:80%">
                                                    Address
                                                </th>
                                                <th style="width:10%">
                                                    {{ get_value_from_list($address_field_labels, 'is_primary') }}
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
                                                    <x-inputs.text-field type="hidden" name="addresses[(sr-id)][id]"
                                                        label="" />
                                                </td>
                                                <td>
                                                    <div class="form-group mb-2">
                                                        <?php
                                                        $field = 'type';
                                                        $label = get_value_from_list($address_field_labels, $field, $field);
                                                        ?>
                                                        <x-inputs.drop-down name="addresses[(sr-id)][{{ $field }}]"
                                                            :label="$label"
                                                            :list="$address_type_list"
                                                            class="will-required will-select2" />
                                                    </div>

                                                    <div class="form-group mb-2">
                                                        <?php
                                                        $field = 'addr1';
                                                        $label = get_value_from_list($address_field_labels, $field, $field);
                                                        ?>
                                                        <x-inputs.text-field name="addresses[(sr-id)][{{ $field }}]"
                                                            :label="$label" class="will-required" />
                                                    </div>
                                                    <div class="form-group mb-2">
                                                        <?php
                                                        $field = 'addr2';
                                                        $label = get_value_from_list($address_field_labels, $field, $field);
                                                        ?>
                                                        <x-inputs.text-field name="addresses[(sr-id)][{{ $field }}]"
                                                            :label="$label" />
                                                    </div>

                                                    <div class="form-group mb-2">
                                                        <?php
                                                        $field = 'pincode';
                                                        $label = get_value_from_list($address_field_labels, $field, $field);
                                                        ?>
                                                        <x-inputs.text-field name="addresses[(sr-id)][{{ $field }}]"
                                                            :label="$label" class="will-required validate-int" />
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'is_primary';
                                                    ?>
                                                    <x-inputs.checkbox name="addresses[(sr-id)][{{ $field }}]"
                                                        label="" class="is_primary" />
                                                </td>
                                            </tr>
                                            @php
                                                $addresses = old('addresses', $addresses ?? []);
                                                unset($addresses['(sr-id)']);
                                                // d($addresses, true);
                                            @endphp
                                            @if (isset($addresses))
                                                @foreach ($addresses as $k => $child_arr)
                                                    <tr class="" sr-id="{{ $child_arr['id'] }}" sr-counter="{{ $k }}">
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
                                                                        <i
                                                                            class=" fas fa-times-circle text-danger icon"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="addresses[{{ $k }}][id]"
                                                                value="{{ $child_arr['id'] ?? '' }}" />
                                                        </td>
                                                        <td>
                                                            <div class="form-group mb-2">
                                                                <?php
                                                                $field = 'type';
                                                                $value = $child_arr[$field] ?? '';
                                                                $label = get_value_from_list($address_field_labels, $field, $field);
                                                                ?>
                                                                <x-inputs.drop-down name="addresses[{{ $k }}][{{ $field }}]"
                                                                    :label="$label"
                                                                    :value="$value"
                                                                    :list="$address_type_list"
                                                                    class="will-required will-select2" />
                                                            </div>

                                                            <div class="form-group mb-2">
                                                                <?php
                                                                $field = 'addr1';
                                                                $value = $child_arr[$field] ?? '';
                                                                $label = get_value_from_list($address_field_labels, $field, $field);
                                                                ?>
                                                                <x-inputs.text-field name="addresses[{{ $k }}][{{ $field }}]"
                                                                    :label="$label" :value="$value" class="will-required" />
                                                            </div>
                                                            <div class="form-group mb-2">
                                                                <?php
                                                                $field = 'addr2';
                                                                $value = $child_arr[$field] ?? '';
                                                                $label = get_value_from_list($address_field_labels, $field, $field);
                                                                ?>
                                                                <x-inputs.text-field name="addresses[{{ $k }}][{{ $field }}]"
                                                                    :label="$label" :value="$value" />
                                                            </div>

                                                            <div class="form-group mb-2">
                                                                <?php
                                                                $field = 'pincode';
                                                                $value = $child_arr[$field] ?? '';
                                                                $label = get_value_from_list($address_field_labels, $field, $field);
                                                                ?>
                                                                <x-inputs.text-field name="addresses[{{ $k }}][{{ $field }}]"
                                                                    :label="$label" :value="$value" class="will-required validate-int" />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $field = 'is_primary';
                                                            $value = $child_arr[$field] ?? '';
                                                            ?>
                                                            <x-inputs.checkbox
                                                                name="addresses[{{ $k }}][{{ $field }}]"
                                                                label="" :value="$value" class="is_primary" />
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

                            <?php
                            $field = 'payment_method_type';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                :list="$payment_method_type_list"
                                :value="$model->{$field}"
                                layout="horizontal"
                                class="select2"
                                />
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
    </div>

    </form>

    <script>
        var district_cities = JSON.parse('<?= json_encode($district_cities) ?>');
        $(function() {
            suggestion_list($("#remarks"), $("#remarks-suggestion-box"), [], "/tags/ajax_get_list");

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

            $("#leadFile").browseGallery({
                opener: "#files-choose"
            });

            $("#deal_type").change(function(){

                var v =  $(this).val();

                $(".deal_type-block").hide();
                $(".deal_type-block").find("input,select").attr("disabled", true);

                if (v)
                {
                    $(".deal_type-block-" + v).show();
                    $(".deal_type-block-" + v).find("input,select").removeAttr("disabled");
                }
            });

            $("#state_id").cascade({
                beforeGet: function (src, url) {
                    $.loader.init().setInfo("Fetching City ...").show();
                    return url;
                },
                afterGet: function (src, dest, response) {
                    $.loader.init().hide();
                    return response;
                },
                default_data : district_cities
            }).trigger("sr-cascade.change", {
                pageLoad: true
            });

            var customerSearchEngine = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.whitespace,
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: '/customer-ajax_get_list?query=%QUERY',
                    wildcard: '%QUERY',
                    transform: function(response) {
                        var result = $.map(response['data'], function(item) {
                            return item;
                        });

                        console.log(result)
                        return result;
                    }
                },
            });

            $('#mobile').typeahead({
                hint: true,
                highlight: true,
                minLength: 1
            }, {
                name: 'mobile',
                display : 'display',
                source: customerSearchEngine,
                templates: {
                    suggestion: function(data) {
                        return `<div>
                            <a href="${data.url}">
                                <span> ${data.display}</span>
                            </a>
                        </div>
                    `;
                    }
                }
            }).bind('typeahead:select', function(ev, suggestion) {
                if (typeof suggestion.url == "string")
                {
                    $.loader.init().setInfo("Redirecting....").show();
                    window.location.href = suggestion.url;
                }
            });

            $.events.execAtLast(function() {
                $("#state_id").trigger("sr-cascade.change", {
                    pageLoad: true
                });

                $("#city_id").change(function(){
                    var v = $(this).val();

                    if (v)
                    {
                        for(var i in district_cities)
                        {
                            if (district_cities[i]['id'] == v)
                            {
                                $("#state_id").val(district_cities[i]['state_id']).trigger('change', {eventBubble : false});
                                return;
                            }
                        }
                    }
                });

                $("#deal_type").trigger("change");

                var template_trs = $(".template-table").find("> tbody > tr").not(".sr-table-template-row");
                template_trs.find(".will-required").attr("required", true);

                template_trs.find(".will-select2").select2({
                    placeHolder: "Please Select",
                    theme: "bootstrap-5",
                });

            });
        })
    </script>
@endsection
