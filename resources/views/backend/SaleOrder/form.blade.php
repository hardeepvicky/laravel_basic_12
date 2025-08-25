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
                            <?php
                            $field = 'order_no';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field layout="horizontal" :id="$field" :name="$field" :label="$label"
                                value="{{ $model->{$field} }}" placeholder="Enter {{ $label }}"
                                mandatory="true" disabled="true"/>

                            <?php
                            $field = 'dealer_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down layout="horizontal" :id="$field" :name="$field" :label="$label"
                            :list="$dealer_list" value="{{ $model->{$field} }}" class="select2"/>

                            <?php
                            $field = 'customer_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down layout="horizontal" :id="$field" :name="$field" :label="$label"
                                :list="$customer_list" value="{{ $model->{$field} }}" class="select2"/>

                            <?php
                            $field = 'estimated_dispatch_date';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field layout="horizontal" :id="$field" :name="$field" :label="$label"
                                value="{{ $model->{$field} }}" placeholder="Enter {{ $label }}"
                                mandatory="true" class="date-picker"/>

                            
                            <div class="textarea-wrapper">
                                <?php
                                $field = 'remarks';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-area layout="horizontal" :id="$field" :name="$field" :label="$label"
                                    value="{{ $model->{$field} }}" class="auto-suggesstion-list" rows="3"
                                    placeholder="Enter {{ $label }}" />
                                <div id="{{ $field . '-suggestion-box' }}" class="suggestion-box hidden"></div>
                            </div>
                        </div>
                        <div class="col-md-12 col-xxl-12">
                            <h4 class="b-b-info">
                                Products
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
                                            {{ get_value_from_list($sale_order_product_field_labels, 'product_id') }}
                                        </th>
                                        <th style="width:20%">
                                            {{ get_value_from_list($sale_order_product_field_labels, 'make_id') }}
                                        </th>
                                        <th style="width:20%">
                                            {{ get_value_from_list($sale_order_product_field_labels, 'order_qty') }}
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
                                            <x-Inputs.text-field id="{{ $field }}-(sr-id)" :name="$field" :label="$false" class="child_product_id"/>
                                            <div class="gallery-files-info m-1">
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
                                            $field = 'order_qty';
                                            ?>
                                            <x-Inputs.text-field type="number" id="{{ $field }}-(sr-id)" name="children[(sr-id)][{{ $field }}]"
                                                :label="$false"
                                                class="will-required validate-int order_qty" />
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
                                                    <x-Inputs.text-field id="{{ $field }}-(sr-id)" :name="$field" :label="$false" class="child_product_id"  :value="$value" class="child_product_id"/>
                                                    <div class="gallery-files-info m-1">
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'make_id';
                                                    $value = $child_arr[$field] ?? '';
                                                    ?>
                                                    <x-Inputs.drop-down id="{{ $field }}-(sr-id)"
                                                        name="children[(sr-id)][{{ $field }}]" :label="$false" :value="$value"
                                                        :list="$maker_list" class="will-required will-select2 make_id" />
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'order_qty';
                                                    $value = $child_arr[$field] ?? '';
                                                    ?>
                                                    <x-Inputs.text-field type="number" id="{{ $field }}-(sr-id)" name="children[(sr-id)][{{ $field }}]"
                                                        :label="$false" :value="$value"
                                                        class="will-required validate-int order_qty" />
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

            <x-Backend.form-common-footer-buttons />
        </div>
    </div>
    </form>

    <script>
        $(function() {
            var model_id = '<?= $model->id ?? 0 ?>';

            function int_child_product_id(_tr, last_id)
            {
                _tr.find(".child_product_id").browseProductGallery({
                    opener: _tr.find(".choose-child_product_id"),
                    postfix: "choose-child_product_id-" + last_id,
                    onSelect : function(id_list)
                    {

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

                    int_child_product_id(_tr, last_id);
                }
            });

            var trs = $(".template-table").find(">tbody>tr").not(".sr-table-template-row");
            trs.each(function(index, _tr){
                int_child_product_id($(_tr), index);
            });

            $.events.execAtLast(function() {

            });
        });
    </script>
@endsection
