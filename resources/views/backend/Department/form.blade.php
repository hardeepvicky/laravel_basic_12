@extends($layout)

@section('content')
<<<<<<< HEAD

<?php
$page_header_links = [
    ["title" => "Summary", "url" => route($routePrefix . ".index")]
];
?>

@include($partial_path . ".page_header")

<x-Backend.form-errors />

<form action="{{ $form['url'] }}" method="POST" class="">
    {!! csrf_field() !!}
    {{ method_field($form['method']) }}

    <div class="row">
        <div class="offset-md-2 col-md-8">
            <div class="card">
                <div class="card-header card-no-border pb-0">
                    <h3>Basic</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <x-Inputs.text-field name="name" label="Name" 
                                    :value="$model->name"
                                    placeholder="Enter Name" 
                                    autocomplete="off"
                                    mandatory="true"
                                    />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3 checkbox-checked">
                                <x-Inputs.checkbox name="is_active" label="Active" :value="$model->is_active" />
=======
    <?php
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />

    <div class="row">
        <div class="col-md-12 col-xxl-10">
            <div class="card">
                <div class="card-header card-no-border pb-0">

                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                    placeholder="Enter {{ $label }}" mandatory="true" autocomplete="off" />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'short_name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$model->{$field}"
                                    placeholder="Enter {{ $label }}" mandatory="true" autocomplete="off" />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'dept_head_user_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :name="$field" :label="$label" :value="$model->{$field}"
                                    :list="$user_list"
                                    class="select2"
                                    />
                            </div>
                            <div class="form-group mb-3">
                                <?php
                                $field = 'list_display_order';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field type="number" :name="$field" :label="$label"
                                    value="{{ $model->{$field} }}" placeholder="Enter {{ $label }}" mandatory="true"
                                    autocomplete="off" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group mb-3">
                                    <?php
                                    $field = 'is_active';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.checkbox :name="$field" :label="$label" value="{{ $model->{$field} }}" />
                                </div>
>>>>>>> base_v3
                            </div>
                        </div>
                    </div>
                </div>
<<<<<<< HEAD
            </div>

            <x-Backend.form-common-footer-buttons />
        </div>
    </div>
</form>

@endsection
=======
                <x-Backend.form-common-footer-buttons />
            </div>


        </div>
    </div>
    </form>
@endsection
>>>>>>> base_v3
