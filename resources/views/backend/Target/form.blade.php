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
                            <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                placeholder="Enter {{ $label }}"
                                layout="horizontal"
                                mandatory="true" />

                            <?php
                            $field = 'end_date';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                placeholder="Enter {{ $label }}"
                                class="date-picker"
                                layout="horizontal"
                                mandatory="true" />

                            <?php
                                $field = 'list_display_order';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field type="number" layout="horizontal" :name="$field" :label="$label"
                                    :value="$model->{$field}" placeholder="Enter {{ $label }}"
                                    mandatory="true" autocomplete="off" />
                                    
                            <div class="row">
                                <div class="col-sm-3">
                                    
                                </div>
                                <div class="col-sm-9" style="padding-top: 10px">
                                    <div class="form-group mb-3">
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

                    _tr.find(".will-date-picker").flatpickr({
                        dateFormat: constants.dateFormat
                    });
                }
            });

            $("#projectFile").browseGallery({
                opener: "#files-choose"
            });

            $.events.execAtLast(function() {
            });
        })
    </script>
@endsection
