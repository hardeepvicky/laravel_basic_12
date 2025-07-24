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

            <div class="form-group">
                <x-Inputs.text-field type="text" name="name" label="Name" placeholder="Enter Name" />
            </div>
            <div class="form-group">
                <x-Inputs.text-field type="email" name="email" label="Email" placeholder="Enter Email" />
            </div>
            <div class="form-group">
                <x-Inputs.text-field type="password" name="password" label="Password" placeholder="Enter Password" />
            </div>
            <div class="form-group">
                <x-Inputs.text-field type="password" name="password_confirmation" label="Confirm Password"
                    placeholder="Enter Confirm Password" />
            </div>
            
            <div class="form-group">
                <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">Register</button>
            </div>
        </form>

        @include('auth.partials.footer')
    </div>
@endsection
