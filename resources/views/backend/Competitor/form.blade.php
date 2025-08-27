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
                            $field = 'name';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label" 
                                :value="$model->{$field}"
                                layout="horizontal"
                                placeholder="Enter {{ $label }}" 
                                mandatory="true" />


                            <?php
                            $field = 'alias';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label" 
                                :value="$model->{$field}"
                                layout="horizontal"
                                placeholder="Enter {{ $label }}" />

                            <?php
                            $field = 'mobile';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label"
                                :value="$model->{$field}" 
                                layout="horizontal"
                                class="validate-mobile"
                                placeholder="Enter {{ $label }}" 
                                mandatory="true" />

                            <?php
                            $field = 'mobile2';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label"
                                :value="$model->{$field}" 
                                layout="horizontal"
                                class="validate-mobile"
                                placeholder="Enter {{ $label }}" />

                            <?php
                            $field = 'phone_no';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label"
                                :value="$model->{$field}" 
                                layout="horizontal"
                                class="validate-phone"
                                placeholder="Enter {{ $label }}" />

                            <?php
                            $field = 'company_name';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label"
                                :value="$model->{$field}"
                                layout="horizontal"
                                placeholder="Enter {{ $label }}" />

                            <?php
                            $field = 'gst_no';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label"
                                :value="$model->{$field}" 
                                layout="horizontal"
                                placeholder="Enter {{ $label }}"  />

                            <?php
                            $field = 'state_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                :list="$state_list" 
                                :value="$model->{$field}"
                                layout="horizontal"
                                class="select2 cascade-drop-down" 
                                data-sr-cascade-target="#district_id"
                                data-sr-cascade-url="/district/ajax_get_list/{v}" 
                                mandatory="true" />

                            <?php
                            $field = 'district_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                :list="$empty_list" 
                                :value="$model->{$field}"
                                layout="horizontal"
                                class="select2 cascade-drop-down" 
                                data-sr-cascade-target="#city_id"
                                data-sr-cascade-url="/city/ajax_get_list/0/{v}" 
                                mandatory="true" />

                            <?php
                            $field = 'city_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                :list="$empty_list" 
                                layout="horizontal"
                                :value="$model->{$field}" 
                                class="select2"
                                mandatory="true" />

                            <?php
                            $field = "competitorProductCategory";
                            $label = get_value_from_list($field_labels, $field, $field);
                            $value = implode(',', $model->competitorProductCategory?->pluck('product_category_id')->toArray());
                            ?>
                            <x-Inputs.drop-down name="competitorProductCategory[]" :label="$label" :list="$product_category_list" 
                                :value="$value"
                                layout="horizontal"
                                class="select2" 
                                multiple="multiple"  />

                            <div class="row mb-3">
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
                                            $addresses = old('addresses', $model->address?->toArray() ?? []);
                                            unset($addresses['(sr-id)']);
                                            // d($addresses, true);
                                        @endphp
                                        @if (isset($addresses))
                                            @foreach ($addresses as $k => $child_arr)
                                                <tr class="" sr-id="{{ $child_arr['id'] }}" sr-counter="{{ $k }}">
                                                    <td>
                                                        <div class="block">
                                                            <div class="left-block">
                                                                
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

                            <div class="row mb-3">
                                <h4 class="b-b-info">
                                    Links
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
                                                Link
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
                                                <x-inputs.text-field type="hidden" name="links[(sr-id)][id]"
                                                    label="" />
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <?php
                                                    $field = 'link';
                                                    ?>
                                                    <x-inputs.text-field name="links[(sr-id)][{{ $field }}]"
                                                        :label="$false" class="will-required" />
                                                </div>
                                            </td>
                                        </tr>
                                        @php
                                            $links = old('links', $model->links?->toArray() ?? []);
                                            unset($links['(sr-id)']);
                                            // d($links, true);
                                        @endphp
                                        @if (isset($links))
                                            @foreach ($links as $k => $child_arr)
                                                <tr class="" sr-id="{{ $child_arr['id'] }}" sr-counter="{{ $k }}">
                                                    <td>
                                                        <div class="block">
                                                            <div class="left-block">
                                                                
                                                            </div>
                                                            <div class="right-block">
                                                                <span class="sr-table-template-delete">
                                                                    <i
                                                                        class=" fas fa-times-circle text-danger icon"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="links[{{ $k }}][id]"
                                                            value="{{ $child_arr['id'] ?? '' }}" />
                                                    </td>                                                        
                                                    <td>
                                                        <div class="form-group mb-2">
                                                            <?php
                                                            $field = 'link';
                                                            $value = $child_arr[$field] ?? '';
                                                            ?>
                                                            <x-inputs.text-field name="links[{{ $k }}][{{ $field }}]"
                                                                :label="$false" :value="$value" class="will-required" />
                                                        </div>
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
                            
                            <?php
                            $field = 'description';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-area :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}"
                                rows="3" 
                                layout="horizontal"
                                placeholder="Enter {{ $label }}" />

                            <?php
                            $field = 'files';
                            $label = "Files";
                            $value = implode(',', $model->files?->pluck('document_file_id')->toArray());
                            ?>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <label class="form-label">
                                        {{ $label }}
                                    </label>
                                </div>
                                <div class="col-sm-9">
                                    <x-Inputs.text-field :id="$field" :name="$field" :label="$false"
                                        :value="$value" multiple="multiple" />
                                    <span id="files-choose" class="btn btn-secondary">Choose Files</span>
                                </div>
                            </div>

                            <?php
                            $field = 'list_display_order';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field type="number" :name="$field" :label="$label"
                                :value="$model->{$field}" 
                                layout="horizontal"
                                placeholder="Enter {{ $label }}"
                                mandatory="true" 
                                autocomplete="off" />

                            <?php
                            $field = 'is_active';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.checkbox :name="$field" :label="$label"
                                :value="$model->{$field}" />
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
            suggestion_list($("#description"), $("#description-suggestion-box"), [], "/tags/ajax_get_list");

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

            $("#files").browseGallery({
                opener: "#files-choose"
            });

            $.events.execAtLast(function() {
                $("#state_id").trigger("sr-cascade.change", {
                    pageLoad: true
                });

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
