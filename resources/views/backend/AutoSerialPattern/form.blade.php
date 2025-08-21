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
                                <div class="form-group mb-3">
                                    <?php
                                    $field = 'type';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                        :list="$type_list" value="{{ $model->{$field} }}" class="select2"
                                        mandatory="true"
                                        />
                                </div>
                                <div class="form-group mb-3">
                                    <?php
                                    $field = 'pattern';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                        placeholder="Enter {{ $label }}" mandatory="true" />
                                    <div class="help-block">
                                        {mmm}/{yyyy}-{counter}
                                        <ul class="b-t-dark">
                                            <li>12 : {mm}</li>
                                            <li>Jan : {mmm}</li>
                                            <li>96 : {yy}</li>
                                            <li>1996 : {yyyy}</li>
                                            <li>counter* : {counter}</li>
                                        </ul>
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
@endsection
