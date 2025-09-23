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
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <x-Inputs.drop-down name="state_id" label="States"
                                    :list="$state_list"
                                    :value="$model->state_id"
                                    class="select2"/>
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
=======
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
                                $field = 'state_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$state_list" 
                                    :value="$model->{$field}" 
                                    class="select2 cascade-drop-down" 
                                    layout="horizontal"
                                    data-sr-cascade-target="#district_id"
                                    data-sr-cascade-url="/district/ajax_get_list/{v}" 
                                    mandatory="true"
                                    />

                                <?php
                                $field = 'district_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$empty_list" :value="$model->{$field}" 
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
>>>>>>> base_v3
