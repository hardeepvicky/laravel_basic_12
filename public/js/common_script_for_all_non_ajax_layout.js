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
        typeof xhr.responseText == "string" &&
        xhr.responseText.length > 0
    ) {
        $.events.onAjaxError(errorString, xhr.responseText, {
            url: settings.url,
        });
    }
});

$(function(){
    $("input.timezone").val(getTimeZone());
});