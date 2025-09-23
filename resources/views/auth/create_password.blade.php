@extends($layout)

@section('content')
    <div class="login-main">
        <form class="theme-form" method="POST">
            @csrf
            <h2 class="text-center">Forgot Password</h2>

            <x-Backend.session-flash />

            <div class="form-group mb-3">
                <x-inputs.text-field type="password" id="password" name="password" label="New Password"
                    placeholder="Enter New Password" :mandatory="true" />
            </div>
            <div class="form-group mb-3">
                <x-inputs.text-field type="password" id="password_confirmation" name="password_confirmation"
                    label="Confirm Password" placeholder="Enter Confirm Password" :mandatory="true" />
            </div>
            <div class="mb-3">
                <button class="btn btn-primary btn-block w-100" type="submit">Change Password</button>
            </div>
        </form>
    </div>
@endsection
