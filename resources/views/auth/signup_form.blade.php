<?php

use App\Models\User;

?>

@extends($layout)

@section('content')
    <div class="login-main">

        <div class="col-md-12 mb-3">
            <a href="{{ route('auth.google.redirect') }}"
                class="btn btn-have-icon d-flex align-items-center justify-content-center signup-with-third-party">
                <i class="fa-brands fa-google"></i>
                Signup with Google
            </a>
        </div>

        <div class="d-flex align-items-center my-4">
            <hr class="flex-grow-1">
            <span class="px-3 text-muted text-sm">or</span>
            <hr class="flex-grow-1">
        </div>

        <form class="theme-form" method="POST" action="{{ route('register') }}">
            @csrf
            <x-Inputs.text-field type="hidden" name="type" value="{{ User::TYPE_EXTERNAL }}" />
            <x-Inputs.text-field type="hidden" name="timezone" class="timezone" />
            <h2 class="text-center mb-2">Register</h2>

            <x-Backend.session-flash />
            <x-Backend.form-errors />

            <div class="form-group">
                <x-Inputs.drop-down name="sub_type" label="Type" :list="$sub_type_list" class="select2" mandatory="true" />
            </div>
            <div class="form-group">
                <x-Inputs.drop-down id="country_id" name="country_id" label="Country" :list="$country_list" :value="$model->country_id"
                    class="select2" mandatory="true" />
            </div>

            <div class="form-group">
                <?php
                $field = 'person_name';
                $label = get_value_from_list($field_labels, $field, $field);
                ?>
                <x-Inputs.text-field type="text" :id="$field" :name="$field" :label="$label" :value="$model->{$field}"
                    placeholder="Enter {{ $label }}" mandatory="true" />
            </div>
            <div class="form-group">
                <?php
                $field = 'person_last_name';
                $label = get_value_from_list($field_labels, $field, $field);
                ?>
                <x-Inputs.text-field type="text" :id="$field" :name="$field" :label="$label" :value="$model->{$field}"
                    placeholder="Enter {{ $label }}" mandatory="true" />
            </div>
            <div class="form-group">
                <?php
                $field = 'email';
                $label = get_value_from_list($field_labels, $field, $field);
                ?>
                <x-Inputs.text-field type="email" :id="$field" :name="$field" :label="$label"
                    :value="$model->{$field}" placeholder="Enter {{ $label }}" mandatory="true" />
                <div class="help-block">This will use as login id</div>
            </div>
            <div class="form-group mb-3">
                <?php
                $field = 'mobile';
                $label = get_value_from_list($field_labels, $field, $field);
                ?>
                <x-Inputs.group-text-field :id="$field" :name="$field" :label="$label" class="validate-mobile"
                    :value="$model->{$field}" placeholder="Enter {{ $label }}"
                    secondaryText='<span id="mobile_country_code" class="input-group-text">+91</span>' mandatory="true" />
            </div>
            <div class="form-group mb-3">
                <?php
                $field = 'whatsapp_mobile';
                $label = get_value_from_list($field_labels, $field, $field);
                ?>
                <x-Inputs.group-text-field :id="$field" :name="$field" :label="$label" class="validate-mobile"
                    :value="$model->{$field}" placeholder="Enter {{ $label }}"
                    secondaryText='<span class="input-group-text text-center"><span id="same_as_mobile" class="btn btn-secondary btn-sm">Same As Mobile</span></span>'
                    mandatory="true" />
            </div>

            <div class="form-group">
                <x-Inputs.text-field type="password" name="password" label="Password" placeholder="Enter Password"
                    mandatory="true" />
            </div>
            <div class="form-group">
                <x-Inputs.text-field type="password" name="password_confirmation" label="Confirm Password"
                    placeholder="Enter Confirm Password" mandatory="true" />
            </div>

            <div class="mb-2">
                <x-google-capacha />
            </div>

            <div class="form-group">
                <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">Register</button>
            </div>
        </form>


        <hr>

        <div class="row">
            <div class="col-md-6 mb-3">
                <a href="/signin" class="btn btn-have-icon d-flex align-items-center justify-content-center">
                    <i class="fa-solid fa-user-secret"></i>
                    Signin
                </a>
            </div>

            <div class="col-md-6 mb-3">
                <a href="/login" class="btn btn-have-icon d-flex align-items-center justify-content-center">
                    <i class="fa-solid fa-user-secret"></i>
                    Login
                </a>
            </div>
        </div>

        @include('auth.partials.footer')
    </div>

    <div class="modal fade" id="modal-signup-with-third-party" tabindex="-1" aria-labelledby="modal-add-via-upload"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5">Please give us about below information</h3>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="signup-with-third-party-before-signup"
                        action="{{ route('signup.with.third.party.save.data.before.signup') }}" method="POST">
                        @csrf
                        <x-Inputs.text-field type="hidden" name="timezone" class="timezone" />
                        <div class="form-group">
                            <x-Inputs.drop-down id="sub_type" name="sub_type" label="Type" :list="$sub_type_list"
                                class="select2" mandatory="true" />
                        </div>
                        <div class="form-group">
                            <x-Inputs.drop-down id="country_id" name="country_id" label="Country" :list="$country_list"
                                :value="$model->country_id" class="select2" mandatory="true" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary submit" type="button">Submit</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(function() {

            $("#country_id").change(function() {
                var v = $(this).val();

                var data_value = $(this).attr("data-value");

                if (data_value && !v) {
                    v = data_value;
                }

                if (v) {
                    $("#mobile_country_code").html("");
                    ajaxGetJson("/country/ajax_get/" + v, function(response) {
                        var data = response['data'];

                        $("#mobile_country_code").html("+" + data['phonecode']);
                    });
                }
            });

            $("#same_as_mobile").click(function() {
                $("#whatsapp_mobile").val($("#mobile").val());
            });

            $("a.signup-with-third-party").click(function(e) {
                e.preventDefault();

                var url = $(this).attr("href");

                $("#modal-signup-with-third-party").modal("show");


                $("#modal-signup-with-third-party .submit").unbind("click").bind("click",
                    function() {

                        $("#signup-with-third-party-before-signup").ajaxSubmit({
                            beforeSubmit: function(formData, jqForm, options) {
                                $.loader.setInfo("Please Wait...").show();
                            },
                            success: function(responseText, statusText, xhr,
                                $form) {

                                ajaxHandleResponse(url, responseText, function(
                                    response) {
                                    $.loader.setInfo(
                                        "Redirecting to external URL..."
                                    ).show();

                                    location.href = url;
                                });
                            },
                            error: function() {
                                $.loader.hide();
                            },
                        });
                    });

                return false;
            });
        });
    </script>
@endsection
