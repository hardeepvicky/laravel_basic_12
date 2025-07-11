<?php
use App\Helpers\LaravelExtend;
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
    <!-- App favicon -->
    <link rel="icon" href="{{ get_asset_url('assets/images/favicon.png') }}" type="image/x-icon" />
    <link rel="shortcut icon" href="{{ get_asset_url('assets/images/favicon.png') }}" type="image/x-icon" />
    <!-- Google font-->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Nunito+Sans:opsz,wght@6..12,200;6..12,300;6..12,400;6..12,500;6..12,600;6..12,700;6..12,800;6..12,900;6..12,1000&amp;display=swap" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/fontawesome-min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/vendors/flag-icon.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/iconly-icon.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/bulk-style.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/themify.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ get_asset_url('assets/css/vendors/weather-icons/weather-icons.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/vendors/range.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/vendors/scrollbar.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/vendors/slick.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/vendors/slick-theme.css') }}" />

    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/libs/select2/select2.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/libs/select2/select2-bootstrap-5-theme.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/libs/select2/select2-dark.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/libs/Croppie-2.6.4/croppie.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/libs/fancybox/dist/jquery.fancybox.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/vendors/flatpickr/flatpickr.min.css') }}">    

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@11.1.0/public/assets/styles/choices.min.css">

    

    <!-- Theme Specfic -->
    <link id="color" rel="stylesheet" href="{{ get_asset_url('assets/css/color-1.css') }}" media="screen" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/style.css') }}" />

    <!-- Project related CSS -->
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('libs/loader/loader.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('libs/i-data-table/style.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('libs/style-for-custom-lib.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('css/bootstarp_extend.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('css/common.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('css/backend.css') }}" />

    <script src="{{ get_asset_url('assets/js/vendors/jquery/jquery.min.js') }}"></script>
    <script src="{{ get_asset_url('libs/events.js') }}"></script>

    
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
                <x-Backend.warning />

                <?php $primary_info = LaravelExtend::$info['primary'] ?? []; ?>

                <x-Backend.information :info="$primary_info"/>

                @yield('content')
            </div>
            @include($partial_path . '.footer')
        </div>
    </div>

    <script src="{{ get_asset_url('assets/js/vendors/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/vendors/bootstrap/dist/js/popper.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/vendors/font-awesome/fontawesome-min.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/vendors/feather-icon/feather.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/vendors/feather-icon/custom-script.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/chart/apex-chart/stock-prices.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/slick/slick.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/slick/slick.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/js-datatables/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/js-datatables/datatables/datatable.custom.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/js-datatables/datatables/datatable.custom1.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/sweetalert/sweetalert2.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/libs/bootbox/5.5.3/bootbox.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/flat-pickr/flatpickr.js') }}"></script>
    <script src="{{ get_asset_url('assets/libs/Croppie-2.6.4/croppie.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/libs/fancybox/dist/jquery.fancybox.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/libs/ejs.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/libs/jquery.form.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/libs/typeahead.js-0.11.1/dist/typeahead.bundle.min.js') }}"></script>

    <script src="{{ get_asset_url('assets/js/config.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/script.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/height-equal.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/sidebar.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/scrollbar/simplebar.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/scrollbar/custom.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/range/rangeslider.nostyle.umd.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/range/rangeslider.umd.min.js') }}"></script>

    <script src="{{ get_asset_url('assets/js/theme-customizer/customizer.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/choices.js@11.1.0/public/assets/scripts/choices.min.js"></script>

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
