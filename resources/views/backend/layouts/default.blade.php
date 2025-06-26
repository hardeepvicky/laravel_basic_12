<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Test Laravel" name="description" />
    <meta content="Hardeep" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="/favicon.ico">

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/app.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/preloader.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/icons.min.css') }}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/libs/select2/select2.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/libs/select2/select2-bootstrap.min.css') }}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" />

    <!-- Project related CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/common.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/backend/default.css') }}" />

    <!-- Pre javascript -->
    <script type="text/javascript"  src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
</head>

<body>
    <div class="page-content">
        <div class="container-fluid">

            <x-Backend.session-flash />

            @yield('content')

        </div>
    </div>

    <!-- Theme Required File -->
    <script type="text/javascript" src="{{ asset('assets/libs/bootstrap/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/libs/bootbox/5.5.3/bootbox.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/app.js"></script>

    <!-- Component File -->
    <script type="text/javascript" src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

</body>

</html>
