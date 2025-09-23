//Ajax start
<<<<<<< HEAD
$(document).ready(function()
{
    $("form").find("div.pristine-error").parents(".form-group").addClass("has-danger");
=======
$(document).ready(function () {
    $("form")
        .find("div.pristine-error")
        .parents(".form-group")
        .addClass("has-danger");
>>>>>>> base_v3

    $("input[type='checkbox'].chk-select-all").chkSelectAll();

    $(".select2").select2({
<<<<<<< HEAD
        placeHolder : "Please Select",        
        theme : "bootstrap-5",
    });

    $(".modal-select2").select2({
        placeHolder : "Please Select",        
        theme : "bootstrap-5",
        dropdownParent: $("#modal_form")
=======
        placeHolder: "Please Select",
        theme: "bootstrap-5",
    });

    $(".modal-select2").select2({
        placeHolder: "Please Select",
        theme: "bootstrap-5",
        dropdownParent: $("#modal_form"),
>>>>>>> base_v3
    });

    $(".fancybox").fancybox();

    $(".css-toggler").cssClassToggle();

    $(".ajax-load").ajaxLoad();
<<<<<<< HEAD
});
=======
});
>>>>>>> base_v3
