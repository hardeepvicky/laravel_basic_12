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

    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/fontawesome-min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/vendors/flag-icon.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/iconly-icon.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/bulk-style.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/themify.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ get_asset_url('assets/css/vendors/weather-icons/weather-icons.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/vendors/scrollbar.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/vendors/slick.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/vendors/slick-theme.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/style.css') }}" />

    <!-- Project related CSS -->
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('css/common.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('css/backend.css') }}" />

    <script src="{{ get_asset_url('assets/js/vendors/jquery/jquery.min.js') }}"></script>
    <script src="{{ get_asset_url('libs/events.js') }}"></script>
</head>

<body>
    <div class="page-content">
        <div class="container-fluid">

            <x-Backend.session-flash />

            @yield('content')

        </div>
    </div>

    <script src="{{ get_asset_url('assets/js/vendors/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/vendors/bootstrap/dist/js/popper.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/vendors/font-awesome/fontawesome-min.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/vendors/feather-icon/feather.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/vendors/feather-icon/custom-script.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/slick/slick.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/slick/slick.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/config.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/script.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/height-equal.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/sidebar.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/scrollbar/simplebar.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/scrollbar/custom.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/theme-customizer/customizer.js') }}"></script>

    <!-- Project Lib -->
    <script src="{{ get_asset_url('libs/constants.js') }}"></script>

    <script src="{{ get_asset_url('libs/loader/loader.js') }}"></script>
    <script src="{{ get_asset_url('libs/date-util.js') }}"></script>
    <script src="{{ get_asset_url('libs/jquery-input-validate.js') }}"></script>
    <script src="{{ get_asset_url('libs/jquery_extend.js') }}"></script>
    <script src="{{ get_asset_url('libs/i-data-table/script.js') }}"></script>

    <!-- Project JS -->
    <script src="{{ get_asset_url('js/common_funtions.js') }}"></script>
    <script src="{{ get_asset_url('js/common_script_for_all_non_ajax_layout.js') }}"></script>
    <script src="{{ get_asset_url('js/backend/backend_non_ajax.js') }}"></script>
    <script src="{{ get_asset_url('js/backend/common.js') }}"></script>    
</body>

</html>
