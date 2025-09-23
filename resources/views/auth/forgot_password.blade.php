@extends($layout)

@section('content')
    <div class="login-main">
        <form class="theme-form" method="POST">
            @csrf
            <h2 class="text-center">Forgot Password</h2>

            <x-Backend.session-flash />

            <x-Inputs.text-field type="email" name="email" label="Email" placeholder="Enter Email" />
            <div class="form-group mb-0 checkbox-checked">
                <div class="text-end mt-3">
                    <button class="btn btn-primary btn-block w-100" type="submit">Submit</button>
                </div>
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

        @include("auth.partials.footer")
    </div>
@endsection
