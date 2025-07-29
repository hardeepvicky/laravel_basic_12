//Ajax start
$(document).ready(function()
{
    $("form").find("div.pristine-error").parents(".form-group").addClass("has-danger");

    $("input[type='checkbox'].chk-select-all").chkSelectAll();

    $(".select2").select2({
        placeHolder : "Please Select",
        theme : "bootstrap-5",
    });

    $(".css-toggler").cssClassToggle();

    $(".ajax-load").ajaxLoad();

    $(".fancybox").fancybox();

    $(".i-data-table").idataTable();

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
});