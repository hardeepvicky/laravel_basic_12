
$(function()
{
    $.loader.init();    
    
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
    });

    const elements = document.getElementsByClassName('x-editable');
    if (typeof elements == "object" && elements.length > 0)
    {
        elements.forEach(element => {                
            const popover = new DarkEditable(element, {
                ajaxOptions : {
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                }
            });
        });
    }
});