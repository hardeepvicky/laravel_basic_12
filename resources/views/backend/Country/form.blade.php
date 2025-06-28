@extends($layout)

@section('content')
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
                                    <x-Inputs.text-field name="name" label="Name" :value="$model->name"
                                        placeholder="Enter Name" autocomplete="off" mandatory="true" />
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group mb-3">
                                    <x-Inputs.text-field name="short_name" label="Short Name" :value="$model->short_name"
                                        placeholder="Enter Short Name" autocomplete="off" mandatory="true" />
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group mb-3">
                                    <x-Inputs.text-field name="phonecode" label="Phonecode" :value="$model->phonecode"
                                        placeholder="Enter Phonecode" class="validate-int" autocomplete="off"
                                        mandatory="true" />
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group mb-3">
                                    <x-Inputs.text-field name="timezone" label="Timezone" :value="$model->timezone"
                                        placeholder="Enter Timezone" autocomplete="off" mandatory="true" />
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
