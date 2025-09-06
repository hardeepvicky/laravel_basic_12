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
                                $field = 'iso3';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                    layout="horizontal"
                                    placeholder="Enter {{ $label }}" 
                                    mandatory="true" />

                                <?php
                                $field = 'numeric_code';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                    layout="horizontal"
                                    placeholder="Enter {{ $label }}" 
                                    mandatory="true" />

                                <?php
                                $field = 'iso2';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                    layout="horizontal"
                                    placeholder="Enter {{ $label }}" 
                                    mandatory="true" />

                                <?php
                                $field = 'phonecode';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                    layout="horizontal"
                                    placeholder="Enter {{ $label }}" 
                                    mandatory="true" />

                                <?php
                                $field = 'capital';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                    layout="horizontal"
                                    placeholder="Enter {{ $label }}" 
                                    />

                                <?php
                                $field = 'currency';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                    layout="horizontal"
                                    placeholder="Enter {{ $label }}" 
                                    />

                                <?php
                                $field = 'currency_name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                    layout="horizontal"
                                    placeholder="Enter {{ $label }}" 
                                    />

                                <?php
                                $field = 'currency_symbol';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                    layout="horizontal"
                                    placeholder="Enter {{ $label }}" 
                                    />
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
            $.events.execAtLast(function(){
                $("#state_id").trigger("sr-cascade.change", {
                    pageLoad: true
                });
            })
        });
    </script>
@endsection
