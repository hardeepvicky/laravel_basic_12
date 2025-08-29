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
                            $field = 'type';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$type_list"
                                value="{{ $model->{$field} }}" 
                                layout="horizontal"
                                class="select2" 
                                mandatory="true" />

                            <?php
                            $field = 'name';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                layout="horizontal"
                                placeholder="Enter {{ $label }}" 
                                mandatory="true" />

                            <?php
                            $field = 'short_name';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                layout="horizontal"
                                placeholder="Enter {{ $label }}" />

                            <?php
                            $field = 'description';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-area :name="$field" :label="$label" value="{{ $model->{$field} }}"
                                layout="horizontal"
                                placeholder="Enter {{ $label }}" />

                            <?php
                            $field = 'list_display_order';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field type="number" :name="$field" :label="$label" value="{{ $model->{$field} }}"
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
                                    <x-Inputs.checkbox name="is_active" label="Active" :value="$model->is_active" />
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
