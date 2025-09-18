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
                            $field = 'product_category_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$product_category_list"
                                :value="$model->{$field}"
                                layout="horizontal"
                                class="select2"
                                mandatory="true" />

                            <?php
                            $field = 'name';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                layout="horizontal"
                                placeholder="Enter {{ $label }}" mandatory="true" />



                            <table class="table table-bordered order-column template-table mb-4"
                                data-sr-table-template-min-row="1">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width : 50px;">
                                            <span class="sr-table-template-add">
                                                <i class="fas fa-plus-circle text-success icon"></i>
                                            </span>
                                        </th>
                                        <th style="width:90%">
                                            {{ get_value_from_list($issue_solution_field_labels, 'name') }}
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
                                            <x-inputs.text-field type="hidden" name="issue_solutions[(sr-id)][id]" label="" />
                                        </td>
                                        <td>
                                            <?php
                                            $field = 'name';
                                            ?>
                                            <x-inputs.text-field name="issue_solutions[(sr-id)][{{ $field }}]" label=""
                                                class="will-required" />
                                        </td>
                                    </tr>
                                    @php
                                        $issue_solutions = old('issue_solutions', $model->issueSolution?->toArray() ?? []);
                                        unset($issue_solutions['(sr-id)']);
                                    @endphp
                                    @if (isset($issue_solutions))
                                        @foreach ($issue_solutions as $k => $child_arr)
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
                                                    <input type="hidden" name="issue_solutions[{{ $k }}][id]"
                                                        value="{{ $child_arr['id'] ?? '' }}" />
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'name';
                                                    $value = $child_arr[$field] ?? '';
                                                    ?>
                                                    <x-inputs.text-field
                                                        name="issue_solutions[{{ $k }}][{{ $field }}]"
                                                        label="" class="will-required" :value="$value"
                                                        />
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
                            <x-Inputs.checkbox :name="$field" :label="$label" :value="$model->{$field}" />
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

        $.events.execAtLast(function() {

        });
    })
</script>
@endsection
