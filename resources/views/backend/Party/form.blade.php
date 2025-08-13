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
                            <div class="form-group mb-3">
                                <?php
                                $field = 'type';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$type_list"
                                    value="{{ $model->{$field} }}" class="select2" mandatory="true" />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    placeholder="Enter {{ $label }}" mandatory="true" />
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'mobile';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.text-field :name="$field" :label="$label"
                                            value="{{ $model->{$field} }}" class="validate-mobile"
                                            placeholder="Enter {{ $label }}" mandatory="true" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'mobile2';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.text-field :name="$field" :label="$label"
                                            value="{{ $model->{$field} }}" class="validate-mobile"
                                            placeholder="Enter {{ $label }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'phone_no';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.text-field :name="$field" :label="$label"
                                            value="{{ $model->{$field} }}" class="validate-phone"
                                            placeholder="Enter {{ $label }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'address';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-area :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                    rows="3" placeholder="Enter {{ $label }}" />
                            </div>
                            <div class="form-group mb-3">
                                <div class="textarea-wrapper">
                                    <?php
                                    $field = 'description';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.text-area :id="$field" :name="$field" :label="$label"
                                        rows="3" value="{{ $model->{$field} }}"
                                        placeholder="Enter {{ $label }}" class="auto-suggesstion-list" />
                                    <div id="{{ $field . '-suggestion-box' }}" class="suggestion-box hidden"></div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'partyFile';
                                $label = get_value_from_list($field_labels, $field, $field);
                                $value = implode(',', $model->partyFile?->pluck('document_file_id')->toArray());
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                                    :value="$value" multiple="multiple" />
                                <span id="files-choose" class="btn btn-secondary">Choose Files</span>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="b-b-info">
                                        {{ get_value_from_list($field_labels, 'terms') }}
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
                                                    {{ get_value_from_list($field_labels, 'terms') }}
                                                </th>
                                                <th style="width:10%">
                                                    {{ get_value_from_list($term_field_labels, 'is_active') }}
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
                                                    <x-inputs.text-field type="hidden" name="terms[(sr-id)][id]" label="" />
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'name';
                                                    ?>
                                                    <x-inputs.text-area
                                                        name="terms[(sr-id)][{{ $field }}]"
                                                        label="" class="will-required" rows="2"/>
                                                </td>
                                                <td>
                                                    <?php
                                                    $field = 'is_active';
                                                    ?>
                                                    <x-inputs.checkbox
                                                        name="terms[(sr-id)][{{ $field }}]"
                                                        label="" />
                                                </td>
                                            </tr>
                                            @php
                                                $terms = old(
                                                    'terms',
                                                    $terms ?? [],
                                                );
                                                unset($terms['(sr-id)']);
                                            @endphp
                                            @if (isset($terms))
                                                @foreach ($terms as $k => $child_arr)
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
                                                                        <i
                                                                            class=" fas fa-times-circle text-danger icon"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <input type="hidden"
                                                                name="terms[{{ $k }}][id]"
                                                                value="{{ $child_arr['id'] ?? '' }}" />
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $field = 'name';
                                                            $value = $child_arr[$field] ?? '';
                                                            ?>
                                                            <x-inputs.text-area
                                                                name="terms[{{ $k }}][{{ $field }}]"
                                                                label="" class="will-required" :value="$value" rows="2"/>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $field = 'is_active';
                                                            $value = $child_arr[$field] ?? '';
                                                            ?>
                                                            <x-inputs.checkbox
                                                                name="terms[{{ $k }}][{{ $field }}]"
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
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'list_display_order';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.text-field type="number" :name="$field" :label="$label"
                                            value="{{ $model->{$field} }}" placeholder="Enter {{ $label }}"
                                            mandatory="true" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-xl-6" style="padding-top: 30px">
                                    <div class="form-group mb-3">
                                        <?php
                                        $field = 'is_active';
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        ?>
                                        <x-Inputs.checkbox :name="$field" :label="$label"
                                            value="{{ $model->{$field} }}" />
                                    </div>
                                </div>
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

            var template_trs = $(".template-table").find("> tbody > tr").not(".sr-table-template-row");
            template_trs.find(".will-required").attr("required", true);
            
            $("#partyFile").browseGallery({
                opener: "#files-choose"
            });
        })
    </script>
@endsection
