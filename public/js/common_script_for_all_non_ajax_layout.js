$(document).ready(function() {
    $(".go_back").click(function(){
        window.history.back();
    });
});


$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    cache: false,
<<<<<<< HEAD
});

$(document).ajaxError(function (event, xhr, settings, errorString) {
    if (xhr.status == 403) {
        $.events.onAjaxError(errorString, "Session is expired. Please Login");
    } else if (
=======
    beforeSend : function(xhr)
    {
    },
    complete : function (xhr, status) {
    }
});

$(document).ajaxError(function (event, xhr, settings, errorString) {
    
    $.loader.init().hide();

    if (xhr.status == 422) {
        //laravel validation error        
    } 
    else if (
>>>>>>> base_v3
        typeof xhr.responseText == "string" &&
        xhr.responseText.length > 0
    ) {
        $.events.onAjaxError(errorString, xhr.responseText, {
            url: settings.url,
        });
    }
<<<<<<< HEAD
=======
});

$(function(){
    $("input.timezone").val(getTimeZone());
>>>>>>> base_v3
});