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
                                <x-Inputs.text-field name="code" label="Code" 
                                    :value="$model->code"
                                    placeholder="Enter Code" 
                                    autocomplete="off"
                                    mandatory="true"
                                    />
=======
    <?php
    $page_header_links = [['title' => 'Summary', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <form action="{{ $form['url'] }}" method="POST" class="">
        {!! csrf_field() !!}
        {{ method_field($form['method']) }}

        <div class="row">
            <div class="col-md-12 offset-lg-1 col-lg-10">
                <div class="card">
                    <div class="card-header card-no-border pb-0">
                        <h3>Basic</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group mb-3">
                                    <x-Inputs.drop-down id="country_id" name="country_id" label="Country" :list="$country_list"
                                        :value="$model->country_id" class="select2" mandatory="true" />
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group mb-3">
                                    <x-Inputs.text-field name="name" label="Name" :value="$model->name"
                                        placeholder="Enter Name" autocomplete="off" mandatory="true" />
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group mb-3">
                                    <x-Inputs.text-field name="short_name" label="Short Name" :value="$model->short_name"
                                        placeholder="Enter Short Name" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6">
                                <div class="form-group mb-3">
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
