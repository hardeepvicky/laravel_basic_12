<?php

use App\Models\User;

?>
@extends($layout)

@section('content')
    <div class="login-main">
        <form class="theme-form" method="POST" action="{{ route('check_before_login') }}">
            @csrf
            <x-Inputs.text-field type="hidden" name="user_type" value="{{ User::TYPE_EXTERNAL }}" />
            <x-Inputs.text-field type="hidden" name="timezone" class="timezone" />
            <h2 class="text-center mb-2">Sign In</h2>

            <x-Backend.session-flash />
            <x-Backend.form-errors />

            <x-Inputs.text-field type="email" name="login_field" label="Email" placeholder="Enter Email"
                mandatory="true" />

            <div class="form-group">
                <label class="col-form-label">Password</label>
                <div class="form-input position-relative">
                    <x-Inputs.text-field type="password" name="password" label="" placeholder="*********"
                        mandatory="true" />
                    <div class="show-hide"><span class="show"></span></div>
                </div>
            </div>
            <div class="form-group mb-0 checkbox-checked">
                <div class="d-flex justify-content-between">
                    <x-Inputs.checkbox name="remember" label="Remember" />
                    <a class="link" href="{{ route('forgot_password') }}">Forgot password?</a>
                </div>

                <strong>ReCaptcha:</strong>

                <div class="g-recaptcha" data-sitekey="{{ env('GOOGLE_RECAPTCHA_SITE_KEY') }}"></div>

                @if ($errors->has('g-recaptcha-response'))
                    <span class="text-danger">{{ $errors->first('g-recaptcha-response') }}</span>
                @endif

                <div class="text-end mt-3">
                    <button class="btn btn-primary btn-block w-100" type="submit">Sign In</button>
                </div>
            </div>
        </form>
        
        <div class="d-flex align-items-center my-4">
            <hr class="flex-grow-1">
            <span class="px-3 text-muted text-sm">or</span>
            <hr class="flex-grow-1">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <a href="{{ route('auth.google.redirect') }}"
                    class="btn btn-have-icon d-flex align-items-center justify-content-center signin-with-third-party">
                    <i class="fa-brands fa-google"></i>
                    Signin with Google
                </a>
            </div>
            <div class="col-md-6 mb-3">
                <a href="#" class="btn btn-have-icon d-flex align-items-center justify-content-center signin-with-third-party">
                    <i class="fa-brands fa-apple"></i>
                    Signin with Apple
                </a>
            </div>
            <div class="col-md-6 mb-3">
                <a href="/register" class="btn btn-have-icon d-flex align-items-center justify-content-center">
                    <i class="fa-solid fa-user-secret"></i>
                    Signup
                </a>
            </div>

            <div class="col-md-6 mb-3">
                <a href="/login" class="btn btn-have-icon d-flex align-items-center justify-content-center">
                    <i class="fa-solid fa-user-secret"></i>
                    Login
                </a>
            </div>
        </div>

        @include("auth.partials.footer")
    </div>

    <script>
        $(function(){
            $("a.signin-with-third-party").click(function(e){
                e.preventDefault();

                var url = $(this).attr("href");

                $.loader.setInfo("Please Wait...").show();

                var post_data = {
                    timezone : getTimeZone()
                };
                
                $.post(url, post_data, function(response){
                    ajaxHandleResponse("{{ route('signin.with.third.party.save.data.before.signin') }}", response, function(response){
                        $.loader.setInfo("Redirecting to external URL...").show();

                        location.href = url;
                    });
                })

                return false;
            });
        });
    </script>
@endsection
