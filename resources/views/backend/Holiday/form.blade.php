@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];
    $empty_list = [];
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
                                    layout="horizontal"
                                    placeholder="Enter {{ $label }}"
                                    mandatory="true" />

                                <?php
                                $field = 'for_all_branches';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <div class="row">
                                    <div class="col-sm-3">
                                    </div>
                                    <div class="col-sm-9">
                                        <x-Inputs.checkbox :id="$field" :name="$field" :label="$label" :value="$model->{$field}" />
                                    </div>
                                </div>

                                <?php
                                $field = 'branches';
                                $label = "Branch";
                                ?>
                                <x-Inputs.drop-down :id="$field" name="branches[]" :label="$label"
                                    :list="$branch_list" :value="$model->{$field}"
                                    layout="horizontal"
                                    class="select2"
                                    mandatory="true"
                                />

                                <?php
                                $field = 'dates';
                                $label = "Dates";
                                ?>
                                <x-Inputs.text-field name="dates" :label="$label"
                                    :value="$model->{$field}"
                                    class="multi-date-picker"
                                    layout="horizontal"
                                    placeholder="Enter {{ $label }}"
                                    mandatory="true"
                                    autocomplete="off" />

                            </div>
                        </div>
                    </div>
                </div>

                <x-Backend.form-common-footer-buttons />
            </div>
        </div>
    </form>

    <script>
        $(function(){
            $(".multi-date-picker").flatpickr({
                dateFormat: constants.dateFormat,
                mode: "multiple"
            });

            $("#for_all_branches").change(function(){
                if(this.checked)
                {
                    $("#branches").attr("disabled", "true");
                }
                else
                {
                    $("#branches").removeAttr("disabled");
                }
            }).trigger("change", {pageLoad : true});
        });
    </script>
@endsection
