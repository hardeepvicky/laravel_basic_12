@extends($layout)

@section('content')
    <div class="login-main">
        <form class="theme-form" method="POST" action="{{ route('login') }}">
            @csrf
            <h2 class="text-center">Sign in to account</h2>
            <p class="text-center">Enter your email &amp; password to login</p>

            <x-Backend.session-flash />

            <x-Inputs.text-field type="email" name="email" label="Email" placeholder="Enter Email" />
            <div class="form-group">
                <label class="col-form-label">Password</label>
                <div class="form-input position-relative">
                    <input class="form-control" type="password" name="password" required="" placeholder="*********">
                    <div class="show-hide"><span class="show"></span></div>
                </div>
            </div>
            <div class="form-group mb-0 checkbox-checked">
                <a class="link" href="{{ route('forgot_password') }}">Forgot password?</a>
                <div class="text-end mt-3">
                    <button class="btn btn-primary btn-block w-100" type="submit">Sign in </button>
                </div>
            </div>

            <div class="d-flex align-items-center my-4">
                <hr class="flex-grow-1">
                <span class="px-3 text-muted text-sm">OR</span>
                <hr class="flex-grow-1">
            </div>

            <a href="{{ route('auth.google.redirect') }}"
                class="btn btn-lg google-btn d-flex align-items-center justify-content-center">
                <i class="fa-brands fa-google"></i>
                Login with Google
            </a>
        </form>


        <div class="mt-4 text-center text-sm text-gray-500">
            By signing in, you agree to our <a href="#" class="font-medium text-blue-600 hover:text-blue-500">Terms of
                Service</a> and <a href="#" class="font-medium text-blue-600 hover:text-blue-500">Privacy Policy</a>.
        </div>
    </div>
@endsection
