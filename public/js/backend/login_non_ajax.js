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
        dateFormat: constants.dateFormat
    });

    $(".date-time-picker").flatpickr({
        enableTime: true,        
        dateFormat: constants.dateTimeFormat
    });

    $(".time-picker").flatpickr({
        noCalendar: true,
        enableTime: true,
        dateFormat: constants.timeFormat,
    });

    // flatpickr(".date-month-picker", {
    //     disableMobile: "true",
    //     plugins: [
    //         new monthSelectPlugin({
    //             shorthand: true,
    //             dateFormat: "m/Y",
    //             altFormat: "F Y",
    //             theme: "material_blue"
    //         })
    //     ]
    // });

    $(".cascade-drop-down").cascade({
        beforeGet: function (src, url) {
            $.loader.init().setInfo("Fetching...").show();
            return url;
        },
        afterGet: function (src, dest, response) {
            return response;
        },
    });

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

    $(document).on("click", "a.activate", function () {
        var _this = $(this).parent();
        var href = $(this).attr("href");

        ajaxGetJson(href, function (response) {
            var html = '<span class="badge bg-success">Active</span>';
            html += "<br/>";
            html +=
                '<a class="de_activate" href="' +
                response["url"] +
                '">De-Activate</a>';
            _this.html(html);
        });

        return false;
    });

    $(document).on("click", "a.de_activate", function () {
        var _this = $(this).parent();
        var href = $(this).attr("href");

        ajaxGetJson(href, function (response) {
            var html = '<span class="badge bg-danger">De-Active</span>';
            html += "<br/>";
            html +=
                '<a class="activate" href="' +
                response["url"] +
                '">Activate</a>';
            _this.html(html);
        });

        return false;
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

    $(document).on("submit", "form.confirm-submit", function () {
        var _this = $(this);
        var msg = _this.data("msg");
        var is_confirm = _this.data("is_confirm");

        if (!is_confirm) {
            msg = msg ? msg : "Are You Sure?";

            confirmDialog(msg, function () {
                _this.attr("data-is_confirm", 1);
                _this.trigger("submit");
            });

            return false;
        }
    });

    $(document).on("click", "a.ajaxify", function(e){
        e.preventDefault();

        var callback = $(this).data("callback");
        var process_info = $(this).data("process_info");

        if (!process_info)
        {
            process_info = "Sending Request....";
        }

        $.loader.init().setInfo(process_info).show();

        $.get($(this).attr("href"), function(response){

            $.loader.hide();

            if (typeof callback == "string")
            {
                eval(`${callback}();`);
            }
            else if (typeof callback == "function")
            {
                callback();
            }
        })

        return false;
    })
});