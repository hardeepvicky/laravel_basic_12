<x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />
<input type="hidden" name="tab_name" value="{{ $model->tab_name }}" />

<div class="row">
    <div class="col-xxxl-12">
        <div class="card">
            <div class="card-header card-no-border pb-0">
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 col-xxl-8">
                        <table class="table table-bordered order-column template-table"
                            data-sr-table-template-min-row="0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width : 50px;">
                                        <span class="sr-table-template-add">
                                            <i class="fas fa-plus-circle text-success icon"></i>
                                        </span>
                                    </th>
                                    <th style="width:20%">
                                        {{ get_value_from_list($specification_field_labels, 'group_name') }}
                                    </th>
                                    <th style="width:30%">
                                        {{ get_value_from_list($specification_field_labels, 'name') }}
                                    </th>
                                    <th style="width:30%">
                                        {{ get_value_from_list($specification_field_labels, 'value') }}
                                    </th>
                                    <th style="width:10%">
                                        {{ get_value_from_list($specification_field_labels, 'is_active') }}
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
                                        <x-inputs.text-field type="hidden" name="specifications[(sr-id)][id]" label="" />
                                    </td>
                                    <td>
                                        <?php
                                        $field = 'group_name';
                                        ?>
                                        <x-inputs.text-field name="specifications[(sr-id)][{{ $field }}]" label=""
                                            class="will-required" />
                                    </td>
                                    <td>
                                        <?php
                                        $field = 'name';
                                        ?>
                                        <x-inputs.text-field name="specifications[(sr-id)][{{ $field }}]" label=""
                                            class="will-required" />
                                    </td>
                                    <td>
                                        <?php
                                        $field = 'value';
                                        ?>
                                        <x-inputs.text-field name="specifications[(sr-id)][{{ $field }}]" label=""
                                            class="will-required" />
                                    </td>
                                    <td>
                                        <?php
                                        $field = 'is_active';
                                        ?>
                                        <x-inputs.checkbox
                                            name="specifications[(sr-id)][{{ $field }}]"
                                            label="" />
                                    </td>
                                </tr>
                                @php
                                    $specifications = old('specifications', $model->specifications?->toArray() ?? []);
                                    unset($specifications['(sr-id)']);
                                @endphp
                                @if (isset($specifications))
                                    @foreach ($specifications as $k => $child_arr)
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
                                                <input type="hidden" name="specifications[{{ $k }}][id]"
                                                    value="{{ $child_arr['id'] ?? '' }}" />
                                            </td>
                                            <td>
                                                <?php
                                                $field = 'group_name';
                                                $value = $child_arr[$field] ?? '';
                                                ?>
                                                <x-inputs.text-field
                                                    name="specifications[{{ $k }}][{{ $field }}]"
                                                    label="" class="will-required" :value="$value"
                                                    />
                                            </td>                                            
                                            <td>
                                                <?php
                                                $field = 'name';
                                                $value = $child_arr[$field] ?? '';
                                                ?>
                                                <x-inputs.text-field
                                                    name="specifications[{{ $k }}][{{ $field }}]"
                                                    label="" class="will-required" :value="$value"
                                                    />
                                            </td>                                            
                                            <td>
                                                <?php
                                                $field = 'value';
                                                $value = $child_arr[$field] ?? '';
                                                ?>
                                                <x-inputs.text-field
                                                    name="specifications[{{ $k }}][{{ $field }}]"
                                                    label="" class="will-required" :value="$value"
                                                    />
                                            </td>       
                                            <td>
                                                <?php
                                                $field = 'is_active';
                                                $value = $child_arr[$field] ?? '';
                                                ?>
                                                <x-inputs.checkbox
                                                    name="specifications[{{ $k }}][{{ $field }}]"
                                                    label="" :value="$value" />
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

        <x-Backend.form-common-footer-buttons buttons="next" />
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

        var template_trs = $(".template-table").find("> tbody > tr").not(".sr-table-template-row");
        template_trs.find(".will-required").attr("required", true);

        $.events.execAtLast(function() {

        });
    })
</script>
