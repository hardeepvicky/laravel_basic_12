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

        @include("auth.partials.footer")
    </div>
@endsection
