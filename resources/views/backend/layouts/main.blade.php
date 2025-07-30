<?php
use App\Helpers\LaravelExtend;

if (!isset($partial_path))
{
    $partial_path = "backend.partials";
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
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/libs/jquery-file-validator/css/file-validator.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/vendors/flatpickr/flatpickr.min.css') }}">

    <!-- Theme Specfic -->
    <link id="color" rel="stylesheet" href="{{ get_asset_url('assets/css/color-1.css') }}" media="screen" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/css/style.css') }}" />

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" />

    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('assets/libs/summernote/summernote.min.css') }}">

    <!-- Project related CSS -->
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('libs/loader/loader.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('libs/i-data-table/style.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('libs/style-for-custom-lib.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('css/bootstarp_extend.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('css/common.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ get_asset_url('css/backend.css') }}" />

    <script src="{{ get_asset_url('assets/js/vendors/jquery/jquery.min.js') }}"></script>
    <script src="{{ get_asset_url('libs/events.js') }}"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
    <script src="{{ get_asset_url('assets/js/custom-card/custom-card.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/config.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/script.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/height-equal.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/sidebar.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/scrollbar/simplebar.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/scrollbar/custom.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/range/rangeslider.nostyle.umd.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/range/rangeslider.umd.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/clipboard/clipboard.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/js/theme-customizer/customizer.js') }}"></script>

    <script src="{{ get_asset_url('assets/libs/Croppie-2.6.4/croppie.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/libs/fancybox/dist/jquery.fancybox.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/libs/ejs.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/libs/jquery.form.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/libs/typeahead.js-0.11.1/dist/typeahead.bundle.min.js') }}"></script>
    <script src="{{ get_asset_url('assets/libs/jquery-file-validator/js/file-validator.js') }}"></script>
    <script src="{{ get_asset_url('assets/libs/summernote/summernote.min.js') }}"></script>

    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

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
    <script src="{{ get_asset_url('js/backend/file-gallery.js') }}"></script>
    <script src="{{ get_asset_url('js/backend/backend_non_ajax.js') }}"></script>
    <script src="{{ get_asset_url('js/common.js') }}"></script>

    <div class="modal fade" id="modal-add-via-upload" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5">Add Via Upload</h3>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary submit" type="button">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-add-via-url" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5">Add Via URL</h3>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary submit" type="button">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {

            function after_modal_load(form_url, modal)
            {
                setTimeout(() => {
                    $('.modal-select2').select2({
                        placeHolder: "Please Select",
                        theme: "bootstrap-5",
                        dropdownParent: modal
                    });
                }, 500);

                modal.find(".modal-body form").ajaxForm({
                    beforeSubmit: function(arr, $form, options) {

                        $.loader.init().setInfo("Uploading....").show();

                        return true;
                    },
                    uploadProgress: function(event, position, total,
                        percentComplete) {
                        $.loader.setInfo("Uploading " +
                            percentComplete + "%");
                    },
                    error: function(xhr, status, errorString) {
                        $.loader.hide();

                        response = xhr.responseText.trim();

                        if (response.length == 0) {
                            $.events.onAjaxError("JSON Parse Error", "Empty Response");
                            return false;
                        }

                        try
                        {
                            var responseJson = JSON.parse(response);
                        }
                        catch(e)
                        {
                            $.events.onAjaxError("JSON Parse Error", response);
                            return false;
                        }

                        modal.find("form").find(".error-message").remove();

                        if (typeof responseJson['errors'] == "object")
                        {
                            for (var field_name in responseJson['errors'])
                            {
                                var errors = responseJson['errors'][field_name];

                                var field = modal.find("form").find("[name='"+ field_name +"']");
                                if (field.length > 0)
                                {
                                    var html = "";

                                    for (var a in errors)
                                    {
                                        html += '<span class="error-message">' + errors[a] + '</span>';
                                    }

                                    field.parent().append(html);
                                }
                            }
                        }
                        else if (typeof responseJson['message'] == "string")
                        {
                            $.events.onUserError(responseJson['message']);
                        }
                    },
                    success: function(responseText, statusText, xhr) {
                        ajaxHandleResponse(form_url, responseText,
                            function(response) {
                                modal.modal("hide");
                                
                                $("form.summary_search").find(".clear_form_search_conditions").trigger("click");

                                if ($("#gallery-modal-main").length > 0)
                                {
                                    $("#gallery-modal-main").refresh();
                                    $("#gallery-modal-main").modal('show');
                                }
                            })
                    }
                });
            }

            $('#modal-add-via-upload').on('show.bs.modal', function() {

                var form_url = "/document-file/create?add_via=upload";

                $('#modal-add-via-upload').find(".modal-body").html("Loading....");

                $('#modal-add-via-upload').find(".modal-body").load(form_url, function()
                {
                    after_modal_load(form_url, $('#modal-add-via-upload'));
                });
            });

            $('#modal-add-via-upload .submit').click(function() {
                $('#modal-add-via-upload form').trigger("submit");
            });

            $('#modal-add-via-url').on('show.bs.modal', function() {

                var form_url = "/document-file/create?add_via=url";

                $('#modal-add-via-url').find(".modal-body").html("Loading....");

                $('#modal-add-via-url').find(".modal-body").load(form_url, function()
                {
                    after_modal_load(form_url, $('#modal-add-via-url'));
                });
            });

            $('#modal-add-via-url .submit').click(function() {
                $('#modal-add-via-url form').trigger("submit");
            });

            $(document).on("click", "a.document-file-archive", function(){
                var _li = $(this).closest("li.folder-box");

                var url = $(this).attr("href");

                ajaxGetJson(url, function(response){
                    _li.remove();
                });

                return false;
            });

            $(document).on("click", "a.document-file-unarchive", function(){
                var _li = $(this).closest("li.folder-box");

                var url = $(this).attr("href");

                ajaxGetJson(url, function(response){
                    _li.remove();
                });

                return false;
            });

            $(document).on("click", ".file-block", function(){
                $(this).toggleClass("active");
            });

            $.events.lastCallbacks.map(function(cb) {
                cb();
            });
        });
    </script>
</body>

</html>
