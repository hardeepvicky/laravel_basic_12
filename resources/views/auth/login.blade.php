<?php

use App\Models\User;

?>
@extends($layout)

@section('content')
    <div class="login-main">
        <form class="theme-form" method="POST" action="{{ route('check_before_login') }}">
            @csrf
            <x-Inputs.text-field type="hidden" name="user_type" value="{{ User::TYPE_INTERNAL }}" />
            <x-Inputs.text-field type="hidden" name="timezone" class="timezone" />
            <h2 class="text-center mb-2">Login</h2>

            <x-Backend.session-flash />
            <x-Backend.form-errors />

            <x-Inputs.text-field name="login_field" label="Email OR Username" placeholder="Enter Email OR Username" />
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

                <div class="text-end mt-3">
                    <button class="btn btn-primary btn-block w-100" type="submit">Login</button>
                </div>
            </div>

            <div class="d-flex align-items-center my-4">
                <hr class="flex-grow-1">
                <span class="px-3 text-muted text-sm">OR</span>
                <hr class="flex-grow-1">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <a href="/signin" class="btn btn-have-icon d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-user-secret"></i>
                        Signin
                    </a>
                </div>
                <div class="col-md-6 mb-3">
                    <a href="{{ route('auto.login') }}"
                        class="btn btn-have-icon d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-user-secret"></i>
                        Auto Login
                    </a>
                </div>
            </div>
        </form>

        @include('auth.partials.footer')
    </div>
@endsection
