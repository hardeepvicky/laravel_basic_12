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
                                $field = 'country_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$country_list" :value="$model->{$field}" 
                                    layout="horizontal"
                                    class="select2" 
                                    mandatory="true"
                                />
                                <?php
                                $field = 'name';
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
                                $field = 'iso3166_2';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                    layout="horizontal"
                                    placeholder="Enter {{ $label }}" 
                                    mandatory="true" />

                                <?php
                                $field = 'timezone';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$timezone_list" :value="$model->{$field}" 
                                    layout="horizontal"
                                    class="select2" 
                                    mandatory="true"
                                />

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
                                <div class="row">
                                    <div class="col-sm-3">
                                    </div>
                                    <div class="col-sm-9">
                                        <x-Inputs.checkbox :id="$field" :name="$field" :label="$label" :value="$model->{$field}" />
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
        $(function(){
            $.events.execAtLast(function(){
                $("#state_id").trigger("sr-cascade.change", {
                    pageLoad: true
                });
            })
        });
    </script>
@endsection
