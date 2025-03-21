@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'Summary', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <x-backend.form-errors />

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
                                    <x-inputs.text-field name="type" label="Type" :value="$model->name"
                                        placeholder="Enter Type" autocomplete="off" mandatory="true" />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <x-inputs.text-field name="name" label="Name" :value="$model->name"
                                        placeholder="Enter Name" autocomplete="off" mandatory="true" />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="">Departments</label>
                                    <select name="department_id" id="" class="select2">
                                        <option value="">Select</option>
                                        @foreach ($records['departments'] as $department)
                                            <option value="{{ $department['id'] }}">{{ $department['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="">Designation</label>
                                    <select name="designation_id" id="" class="select2">
                                        <option value="">Select</option>
                                        @foreach ($records['designations'] as $designation)
                                            <option value="{{ $designation['id'] }}">{{ $designation['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="">Current State</label>
                                    <select name="current_state_id" id="" class="select2">
                                        <option value="">Select</option>
                                        @foreach ($records['states'] as $state)
                                        <option value="{{ $state['id'] }}">{{ $state['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="">Current City</label>
                                    <select name="current_city_id" id="" class="select2">
                                        <option value="">Select</option>
                                        @foreach ($records['cities'] as $city)
                                        <option value="{{ $city['id'] }}">{{ $city['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="">Permanent State</label>
                                    <select name="permanent_state_id" id="" class="select2">
                                        <option value="">Select</option>
                                        @foreach ($records['states'] as $state)
                                            <option value="{{ $state['id'] }}">{{ $state['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="">Permanent City</label>
                                    <select name="permanent_city_id" id="" class="select2">
                                        <option value="">Select</option>
                                        @foreach ($records['cities'] as $city)
                                            <option value="{{ $city['id'] }}">{{ $city['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-3 checkbox-checked">
                                    <x-inputs.checkbox name="is_active" label="Active" :value="$model->is_active" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <x-backend.form-common-footer-buttons />
            </div>
        </div>
    </form>
@endsection
