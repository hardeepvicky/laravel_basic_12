
$(function()
{
    $('img.lazy-load-image').on('load', function() {
        $(this).closest(".image-container").addClass("loaded");

    }).on('error', function() {
        $(this).closest(".image-container").addClass("loaded error");
    });

    $('img.lazy-load-image').each(function(){
        if ($(this)[0].complete) 
        {
            $(this).trigger('load');
        }
    })
});