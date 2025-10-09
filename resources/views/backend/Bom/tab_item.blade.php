<x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />
<input type="hidden" name="tab_name" value="{{ $model->tab_name }}" />

<div class="row">
    <div class="col-md-10 col-xxl-6">
        <?php
        $field = 'remarks';
        $label = get_value_from_list($field_labels, $field, $field);
        ?>
        <x-Inputs.text-area :id="$field" :name="$field" :label="$label" rows="3"
            value="{{ $model->{$field} }}" placeholder="Enter {{ $label }}" layout="horizontal" />
    
        <div class="row mb-3">
            <div class="col-sm-3">
                Items
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
                            <th style="width:60%">
                                {{ get_value_from_list($bom_item_field_labels, 'item_id') }}
                            </th>
                            <th style="width:30%">
                                {{ get_value_from_list($bom_item_field_labels, 'qty') }}
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
                                <x-inputs.text-field type="hidden" name="bomItems[(sr-id)][id]" label="" />
                            </td>
                            <td>
                                <?php
                                $field = 'item_id';
                                ?>
                                <x-Inputs.drop-down name="bomItems[(sr-id)][{{ $field }}]" :label="$false"
                                    :list="$item_list"
                                    class="will-select2 will-required item_id"
                                    />
                            </td>
                            <td>
                               <?php
                                $field = 'qty';
                                ?>
                                <x-Inputs.group-text-field name="bomItems[(sr-id)][{{ $field }}]" :label="$false"
                                        placeholder="Enter {{ $label }}"
                                        class="will-required validate-float qty"                                        
                                        secondaryTextPosition="last"
                                        secondaryText='<span class="input-group-text unit">unit</span>'
                                        />
                            </td>
                        </tr>
                        @php
                            $bomItems = old('bomItems', $model->bomItems?->toArray() ?? []);
                            unset($bomItems['(sr-id)']);
                        @endphp
                        @if (isset($bomItems))
                            @foreach ($bomItems as $k => $child_arr)
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
                                        <input type="hidden" name="bomItems[{{ $k }}][id]" value="{{ $child_arr['id'] ?? '' }}" />
                                    </td>                                    
                                    <td>
                                        <?php
                                        $field = 'item_id';
                                        $value = $child_arr[$field] ?? '';
                                        ?>
                                        <x-Inputs.drop-down name="bomItems[{{ $k }}][{{ $field }}]" :label="$false"
                                            :list="$item_list"
                                            :value="$value"
                                            class="will-select2 will-required item_id"
                                            />
                                    </td>
                                    <td>
                                    <?php
                                        $field = 'qty';
                                        $value = $child_arr[$field] ?? '';
                                        ?>
                                        <x-Inputs.text-field name="bomItems[{{ $k }}][{{ $field }}]" :label="$false"
                                            :value="$value"
                                            class="will-required validate-float qty"
                                            />
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<x-Backend.form-common-footer-buttons buttons="next" />
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

        $(document).on("change", ".item_id", function(){
            var _tr = $(this).closest("tr");

            var v = $(this).val();

            if (v)
            {
                ajaxGetJson("/item/ajax_get_default_uom/" + v, function(response){
                    _tr.find(".unit").html(response['data']['short_name']);
                });
            }
        });

        $("form").submit(function(){

            var item_list = [];
            var will_submit = true;

            $(".item_id").each(function(){
                var v = $(this).val();

                if (v)
                {
                    if ($.inArray(v, item_list) === -1)
                    {
                        item_list.push(v);                        
                    }
                    else
                    {
                        will_submit = false;
                    }
                
                }
            });

            if (!will_submit)
            {
                $.events.onUserError("Duplicate Items Found");
            }

            return will_submit;
        });
    })
</script>