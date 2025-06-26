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
    <!-- App favicon -->
    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon" />
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon" />
    <!-- Google font-->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Nunito+Sans:opsz,wght@6..12,200;6..12,300;6..12,400;6..12,500;6..12,600;6..12,700;6..12,800;6..12,900;6..12,1000&amp;display=swap" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/fontawesome-min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flag-icon.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/iconly-icon.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bulk-style.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/themify.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/weather-icons/weather-icons.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/scrollbar.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/slick.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/slick-theme.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/libs/select2/select2.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/libs/select2/select2-bootstrap-5-theme.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/libs/select2/select2-dark.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/libs/Croppie-2.6.4/croppie.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/libs/fancybox/dist/jquery.fancybox.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">

    <!-- Theme Specfic -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}" />
    <link id="color" rel="stylesheet" href="{{ asset('assets/css/color-1.css') }}" media="screen" />

    <!-- Project related CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('libs/loader/loader.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('libs/i-data-table/style.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('libs/style-for-custom-lib.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/common.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/backend.css') }}" />

    <script src="{{ asset('assets/js/vendors/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('libs/events.js') }}"></script>
</head>

<body>
    <!-- page-wrapper Start-->
    <!-- tap on top starts-->
    <div class="tap-top"><i class="iconly-Arrow-Up icli"></i></div>
    <!-- tap on tap ends-->
    <!-- loader-->
    <div class="loader-wrapper">
        <div class="loader"><span></span><span></span><span></span><span></span><span></span></div>
    </div>
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        @include ($partial_path . '.header')
        <!-- Page Body Start-->
        <div class="page-body-wrapper">
            <!-- Page sidebar start-->
            @include($partial_path . '.sidebar')
            <!-- Page sidebar end-->
            <div class="page-body">
                @yield('content')
            </div>
            @include($partial_path . '.footer')
        </div>
    </div>


    <script src="{{ asset('assets/js/vendors/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendors/bootstrap/dist/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendors/font-awesome/fontawesome-min.js') }}"></script>
    <script src="{{ asset('assets/js/vendors/feather-icon/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendors/feather-icon/custom-script.js') }}"></script>
    <script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
    <script src="{{ asset('assets/js/chart/apex-chart/stock-prices.js') }}"></script>
    <script src="{{ asset('assets/js/slick/slick.min.js') }}"></script>
    <script src="{{ asset('assets/js/slick/slick.js') }}"></script>
    <script src="{{ asset('assets/js/js-datatables/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/js-datatables/datatables/datatable.custom.js') }}"></script>
    <script src="{{ asset('assets/js/js-datatables/datatables/datatable.custom1.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootbox/5.5.3/bootbox.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/flat-pickr/flatpickr.js') }}"></script>    
    <script src="{{ asset('assets/libs/Croppie-2.6.4/croppie.min.js') }}"></script>
    <script src="{{ asset('assets/libs/fancybox/dist/jquery.fancybox.min.js') }}"></script>
    <script src="{{ asset('assets/libs/ejs.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery.form.min.js') }}"></script>

    <script src="{{ asset('assets/js/config.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/height-equal.js') }}"></script>
    <script src="{{ asset('assets/js/sidebar.js') }}"></script>
    <script src="{{ asset('assets/js/scrollbar/simplebar.js') }}"></script>
    <script src="{{ asset('assets/js/scrollbar/custom.js') }}"></script>

    <script src="{{ asset('assets/js/theme-customizer/customizer.js') }}"></script>

    <!-- Project Lib -->
    <script src="{{ asset('libs/constants.js') }}"></script>

    <script src="{{ asset('libs/loader/loader.js') }}"></script>
    <script src="{{ asset('libs/date-util.js') }}"></script>
    <script src="{{ asset('libs/jquery-input-validate.js') }}"></script>
    <script src="{{ asset('libs/jquery_extend.js') }}"></script>
    <script src="{{ asset('libs/i-data-table/script.js') }}"></script>

    <!-- Project JS -->
    <script src="{{ asset('js/common_funtions.js') }}"></script>
    <script src="{{ asset('js/common_script_for_all_non_ajax_layout.js') }}"></script>
    <script src="{{ asset('js/backend/backend_non_ajax.js') }}"></script>
    <script>
        $(function() {
            $.loader.init();
            $.events.lastCallbacks.map(function(cb) {
                cb();
            });
        });
    </script>
</body>

</html>
