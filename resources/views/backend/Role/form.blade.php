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
                                <x-Inputs.checkbox name="is_admin" label="Admin" :value="$model->is_admin" />
                                <x-Inputs.checkbox name="is_active" label="Active" :value="$model->is_active" />
=======
    <?php
    $page_header_links = [['title' => 'Summary', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" />
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
                                    <x-Inputs.text-field name="name" label="Name" :value="$model->name"
                                        placeholder="Enter Name" autocomplete="off" mandatory="true" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-3 checkbox-checked">
                                    <x-Inputs.checkbox name="is_active" label="Active" :value="$model->is_active" />
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
    </form>
@endsection
>>>>>>> base_v3
