<?php
if (!isset($partial_path))
{
    $partial_path = "frontend.partials";
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>
        {{ $page_title ?? config('app.name') }}
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $page_description ?? config('app.description') }}" />
    <meta name="description" content="{{ $page_author ?? config('app.author') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="user-id" content="{{ auth()->id() }}">
    <!-- App favicon -->
    <link rel="icon" href="{{ get_asset_url('assets/images/favicon.png') }}" type="image/x-icon" />
    <link rel="shortcut icon" href="{{ get_asset_url('assets/images/favicon.png') }}" type="image/x-icon" />
    <!-- Google font-->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Nunito+Sans:opsz,wght@6..12,200;6..12,300;6..12,400;6..12,500;6..12,600;6..12,700;6..12,800;6..12,900;6..12,1000&amp;display=swap" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/fontawesome-min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/vendors/flag-icon.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/libs/select2/select2.min.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ get_asset_url('assets/libs/select2/select2-bootstrap-5-theme.min.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ get_asset_url('assets/libs/fancybox/dist/jquery.fancybox.min.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ get_asset_url('assets/css/vendors/flatpickr/flatpickr.min.css') }}">

    <!-- Project related CSS -->
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('libs/loader/loader.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('libs/i-data-table/style.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('libs/style-for-custom-lib.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('css/common.css') }}" />

    <script src="{{ get_asset_url('assets/js/vendors/jquery/jquery.min.js') }}"></script>
    <script src="{{ get_asset_url('libs/events.js') }}"></script>
</head>

<body class="bg-light">

    <div class="container">
        @include ($partial_path . '.header')
        @yield('content')
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXcWpG9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="{{ get_asset_url('assets/js/vendors/font-awesome/fontawesome-min.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/sweetalert/sweetalert2.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/flat-pickr/flatpickr.js') }}"></script>
    <script src="{{ get_asset_url('assets/libs/fancybox/dist/jquery.fancybox.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/libs/jquery.form.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/libs/jquery-file-validator/js/file-validator.js') }}"></script>
    <script src="{{ get_asset_url('assets/frontend/form-validation.js') }}"></script>

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
    <script src="{{ get_asset_url('js/frontend/frontend_non_ajax.js') }}"></script>
    <script src="{{ get_asset_url('js/common.js') }}"></script>    
</body>

</html>
