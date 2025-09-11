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
                                $field = 'docket_no';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                    layout="horizontal"
                                    placeholder="Enter {{ $label }}"
                                    mandatory="true" 
                                    />

                                <?php
                                $field = 'tracking_no';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                    layout="horizontal"
                                    placeholder="Enter {{ $label }}" 
                                    />

                                <?php
                                $field = 'tracking_phone_no';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                    layout="horizontal"
                                    placeholder="Enter {{ $label }}" 
                                    />


                                <?php
                                $field = 'carrier_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$carrier_list" :value="$model->{$field}" 
                                    layout="horizontal"
                                    class="select2" 
                                />

                                <?php
                                $field = 'shipping_fee';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                    layout="horizontal"
                                    placeholder="Enter {{ $label }}" 
                                    class="validate-float"
                                    />

                                <?php
                                $field = 'status_type';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$status_type_list" :value="$model->{$field}" 
                                    layout="horizontal"
                                    class="select2" 
                                    mandatory="true"
                                />

                                <?php
                                $field = 'remarks';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-area :name="$field" :label="$label" :value="$model->{$field}"
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
@endsection
