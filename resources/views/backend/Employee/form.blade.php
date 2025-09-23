@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'Summary', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <form action="{{ $form['url'] }}" method="POST" class="" enctype="multipart/form-data">
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
<<<<<<< HEAD
                                    <label for="">Type</label>
                                    <select name="type" id="" class="select2">
                                        <option value="">Select</option>
                                        @foreach ($records['types'] as $type)
                                            <option value="{{ $type }}"
                                                @if ($model->type == $type) selected @endif>{{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <x-Inputs.text-field name="name" label="Name" :value="$model->name"
                                        placeholder="Enter Name" autocomplete="off" mandatory="true" />
=======
                                    <x-Inputs.text-field name="name" label="Name" :value="$model->name"
                                        placeholder="Enter Name" mandatory="true" />
>>>>>>> base_v3
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
<<<<<<< HEAD
                                    <x-Inputs.drop-down name="department_id" label="Department" :list="$records['departments']"
                                        class="select2" mandatory="true" :value="$model->department_id" />
=======
                                    <x-Inputs.drop-down name="department_id" label="Department" class="select2"
                                        :list="$department_list" :value="$model->department_id" mandatory="true" />
>>>>>>> base_v3
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
<<<<<<< HEAD
                                    <x-Inputs.drop-down name="designation_id" label="Designation" :list="$records['designations']"
                                        class="select2" mandatory="true" :value="$model->designation_id" />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <x-Inputs.drop-down name="current_state_id" id="cState" label="Current State"
                                        :list="$records['states']" class="select2" mandatory="true" :value="$model->current_state_id" />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3 editCity">
                                    <x-Inputs.drop-down name="current_city_id" id="cCity" label="Current City"
                                        class="select2" mandatory="true" :data-id="$model->current_city_id" />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <x-Inputs.drop-down name="permanent_state_id" id="pState" label="Permanent State"
                                        :list="$records['states']" class="select2" mandatory="true" :value="$model->permanent_state_id" />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <x-Inputs.drop-down name="permanent_city_id" id="pCity" label="Permanent City"
                                        class="select2" mandatory="true" :data-id="$model->permanent_city_id" />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <x-Inputs.drop-down name="type_id" label="Type" :list="$records['dtypes']" class="select2"
                                        mandatory="true" :value="$model->type_id" />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <x-Inputs.file-field type="file" label="Image" name="image" />
                                </div>
                            </div>
                        </div>
=======
                                    <x-Inputs.drop-down name="designation_id" label="Designation" :list="$designation_list"
                                        :value="$model->designation_id" mandatory="true" class="select2" />
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <x-Inputs.text-field name="current_address" label="Current Address 1" :value="$model->current_address"
                                        placeholder="Enter House No., Street No., Locality, Area" mandatory="true" />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <x-Inputs.text-field name="current_address2" label="Current Address 2" :value="$model->current_address"
                                        placeholder="Enter Landmark" />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <x-Inputs.drop-down name="current_state_id" label="Current State" :list="$state_list"
                                        class="select2 cascade-drop-down" mandatory="true" :value="$model->current_state_id"
                                        data-sr-cascade-target="#current_city_id"
                                        data-sr-cascade-url="/city/ajax_get_list/{v}" />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <?php
                                    $value = old('current_city_id', $model->current_city_id);
                                    ?>
                                    <x-Inputs.drop-down id="current_city_id" name="current_city_id" label="Current City"
                                        class="select2" mandatory="true" :data-value="$value" />
                                </div>
                            </div>
                            <hr />
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <x-Inputs.text-field name="permanent_address" label="Permanent Address 1"
                                        :value="$model->permanent_address" placeholder="Enter House No., Street No., Locality, Area"
                                        mandatory="true" />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <x-Inputs.text-field name="permanent_address2" label="Permanent Address 2"
                                        :value="$model->permanent_address" placeholder="Enter Landmark" />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <x-Inputs.drop-down name="permanent_state_id" label="Permanent State"
                                        class="select2 cascade-drop-down" :list="$state_list" :value="$model->permanent_state_id"
                                        mandatory="true" data-sr-cascade-target="#permanent_city_id"
                                        data-sr-cascade-url="/city/ajax_get_list/{v}" />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <?php
                                    $value = old('permanent_city_id', $model->permanent_city_id);
                                    ?>
                                    <x-Inputs.drop-down id="permanent_city_id" name="permanent_city_id"
                                        label="Permanent City" class="select2" mandatory="true" :data-value="$value" />
                                </div>
                            </div>
                            <hr />
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group mb-3">
                                    <x-Inputs.text-field id="mobile" name="mobile" label="Mobile" :value="$model->mobile"
                                        placeholder="Enter Mobile No." mandatory="true" />
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group mb-3">
                                    <x-Inputs.group-text-field id="mobile" name="whatsapp_mobile" label="Whatsapp Mobile"
                                        :value="$model->whatsapp_mobile" placeholder="Enter Mobile No." mandatory="true"
                                        secondaryText='<span id="same-as-mobile" class="input-group-text" style="font-size:0.7rem; cursor:pointer;"> Same as Mobile</span>' />
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group mb-3">
                                    <x-Inputs.text-field name="mobile2" label="Alternative Mobile" :value="$model->mobile2"
                                        placeholder="Enter Mobile No." />
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group mb-3">
                                    <x-Inputs.text-field name="email" label="Email" :value="$model->email"
                                        placeholder="Enter Email" />
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group mb-3">
                                    <x-Inputs.text-field name="dob" label="Date Of Birth" :value="$model->dob"
                                        class="form-control date-picker" placeholder="DD-MMM-YYYY" mandatory="true" />
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group mb-3">
                                    <x-Inputs.text-field name="doj" label="Date Of Joining" :value="$model->doj"
                                        class="form-control date-picker" placeholder="DD-MMM-YYYY" mandatory="true" />
                                </div>
                            </div>
                        </div>

>>>>>>> base_v3
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-3 checkbox-checked">
                                    <x-Inputs.checkbox name="is_active" label="Active" :value="$model->is_active" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <x-Backend.form-common-footer-buttons />
            </div>
        </div>
    </form>
<<<<<<< HEAD
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
    $(document).ready(function() {

        var cId = $('#cCity').data('id');

        if (cId && cId != '' && cId != 'undefined') {

            $('#cCity option:eq(0)').remove();

            $.ajax({
                url: '/cityname/' + cId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.cities) {
                        $.each(data.cities, function(id, name) {
                            $('#cCity').append('<option value="' + id + '">' +
                                name + '</option>');
                        });
                    } else {
                        $('#cCity').append('<option value="">No Records Found</option>')
                    }
                }
            })

        }

        var pId = $('#pCity').data('id');

        if (pId && pId != '' && pId != 'undefined') {

            $('#pCity option:eq(0)').remove();

            $.ajax({
                url: '/cityname/' + pId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.cities) {
                        $.each(data.cities, function(id, name) {
                            $('#pCity').append('<option value="' + id + '">' +
                                name + '</option>');
                        });
                    } else {
                        $('#pCity').append('<option value="">No Records Found</option>')
                    }
                }
            })

        }
        // state city dropdown
        function updateCities(stateId, selectedId) {

            $('#' + selectedId).html('<option value="">Select</option>');

            $.ajax({
                url: '/cities/' + stateId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.cities) {
                        $.each(data.cities, function(id, name) {
                            $('#' + selectedId).append('<option value="' + id + '">' +
                                name + '</option>');
                        });
                    } else {
                        $('#' + selectedId).append('<option value="">No Records Found</option>')
                    }
                },
            })

        }

        // current state city
        $('#cState').on('change', function() {
            var stateId = $(this).val();
            if (stateId) {
                updateCities(stateId, 'cCity');
            } else {
                $('#cCity').html('<option value="">Select</option>');
            }
        });

        // permanent state city
        $('#pState').on('change', function() {
            var stateId = $(this).val();
            if (stateId) {
                updateCities(stateId, 'pCity');
            } else {
                $('#pCity').html('<option value="">Select</option>');
            }
        });

    })
</script>
=======


    <script>
        $(document).ready(function() {
            $.events.execAtLast(function() {
                $(".cascade-drop-down").trigger("sr-cascade.change", {
                    pageLoad: true
                });

                $("#same-as-mobile").click(function() {
                    $("input[name='whatsapp_mobile']").val($("input[name='mobile']").val());
                });
            });
        })
    </script>
@endsection
>>>>>>> base_v3
