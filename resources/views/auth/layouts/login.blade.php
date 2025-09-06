<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Admiro admin is super flexible, powerful, clean &amp; modern responsive bootstrap 5 admin template with unlimited possibilities.">
    <meta name="keywords"
        content="admin template, Admiro admin template, best javascript admin, dashboard template, bootstrap admin template, responsive admin template, web app">
    <meta name="author" content="pixelstrap">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>
        {{ $page_title ?? config('app.name') }}
    </title>

    @include("partials.common_css_links");
    
    <link rel="stylesheet" type="text/css" href="/css/backend.css?<?= CSS_VERSION ?>" />    
    <script async src="https://www.google.com/recaptcha/api.js"></script>
</head>

<body>
    <!-- tap on top starts-->
    <div class="tap-top"><i class="iconly-Arrow-Up icli"></i></div>
    <!-- tap on tap ends-->    
    <!-- login page start-->
    <div class="container-fluid p-0">
        <div class="row m-0">
            <div class="col-12 p-0">
                <div class="login-card login-dark">
                    <div>
                        <div>
                            <a class="logo" href="{{ route('home') }}">
                                <img class="img-fluid for-light m-auto" src="/assets/images/logo/logo.png"
                                    alt="looginpage">
                                <img class="img-fluid for-dark" src="/assets/images/logo/logo.png" alt="logo"></a>
                        </div>
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>

        @include("partials.common_script_links");

        <script src="{{ get_asset_url('assets/js/password.js') }}"></script>
        <script src="{{ get_asset_url('js/backend/login_non_ajax.js') }}"></script>
    </div>
</body>

</html>