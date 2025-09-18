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
                            <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                placeholder="Enter {{ $label }}" 
                                layout="horizontal"
                                mandatory="true" />

                            <?php
                            $field = 'description';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-area :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                placeholder="Enter {{ $label }}" 
                                layout="horizontal"
                                />

                            <?php
                            $field = 'list_display_order';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field type="number" :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                placeholder="Enter {{ $label }}" 
                                layout="horizontal"
                                mandatory="true" 
                                autocomplete="off" />

                            <div class="row">
                                <div class="col-sm-3"></div>
                                <div class="col-sm-3">
                                    <?php
                                    $field = 'is_default';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.checkbox :name="$field" :label="$label" value="{{ $model->{$field} }}" />
                                    <?php
                                    $field = 'is_active';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.checkbox :name="$field" :label="$label" value="{{ $model->{$field} }}" />
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
