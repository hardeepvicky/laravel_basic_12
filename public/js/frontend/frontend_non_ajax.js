/**
 * @author : Hardeep
 * this file only for backend
 */

$(document).ready(function () {
    $.loader.init();

    $("form")
        .find("div.pristine-error")
        .parents(".form-group")
        .addClass("has-danger");

    $("input[type='checkbox'].chk-select-all").chkSelectAll();

    $(".css-toggler").cssClassToggle();

    $(".ajax-load").ajaxLoad();

    $(".select2").select2({
        placeHolder: "Please Select",
        theme: "bootstrap-5",
    });

    $(".fancybox").fancybox();

    $(".date-picker").flatpickr({
        dateFormat: "d-M-Y",
    });

    $(".date-time-picker").flatpickr({
        enableTime: true,
        time_24hr: false,
        dateFormat: "d-M-Y h:i K",
    });

    $(".time-picker").flatpickr({
        noCalendar: true,
        enableTime: true,
        dateFormat: "h:i K",
    });

    $(".cascade-drop-down").cascade();

    /** -------------------------------------------------------- */

    $("input.invalid-char").on("keypress", function (event) {
        var regex = new RegExp("^[a-zA-Z0-9~!@#$%^*()<>{}_.,/+-\\\\]+$");
        var key = String.fromCharCode(
            !event.charCode ? event.which : event.charCode
        );
        key = key.trim();

        var error_span = $(this).parent().find(".error-message");
        if (error_span.length == 0) {
            $(this)
                .parent()
                .append(
                    '<span class="error-message">&#9679 Invalid Character</span>'
                );
            var error_span = $(this).parent().find(".error-message");
        }

        if (key && !regex.test(key)) {
            event.preventDefault();
            error_span.show();
            return false;
        }

        error_span.hide();
    });

    $(document).on("click", "a.confirm", function () {
        var _this = $(this).parent();
        var href = $(this).attr("href");
        var msg = $(this).data("msg");
        msg = msg ? msg : "Are You Sure?";

        confirmDialog(msg, function () {
            window.location.href = href;
        });

        return false;
    });
});
