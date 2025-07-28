/**
 * @created 08-Oct-2023
 * @author Hardeep
 */

$.loader = {
    /**
     * this function appends html to body
     * @returns
     */
    jqueryObj : null,

    init: function (selector) {

        if (typeof selector == "undefined")
        {
            selector = "body";
        }
        else
        {
            if ($(selector).length == 0)
            {
                console.error(`${selector} not found by jquery`);
                return;
            }
            else if ($(selector).length > 1)
            {
                console.error(`${selector} more than 1 found by jquery`);
                return;
            }
        }

        this.jqueryObj = $(selector).find(".loader-container");

        if (this.jqueryObj.length > 0) {
            return this;
        }

        var html = '<div class="loader-container" style="display:none">';
            html += '<div class="loader-content">';
                html += '<div class="loader-icon spinner-border"></div>';
                html += '<div class="loader-info" style="display:none"></div>';
                html += '<div class="loader-footer" style="display:none"></div>';
            html += "</div>";
        html += "</div>";

        $(selector).append(html).addClass("loader-applied");

        this.jqueryObj = $(selector).find(".loader-container");

        return this;
    },

    setInfo : function(html) {
        if (html === false)
        {
            this.jqueryObj.find(".loader-info").html("").hide();
        }

        if (typeof(html) == "string")
        {
            this.jqueryObj.find(".loader-info").html(html).show();
        }

        return this;
    },

    setFooter : function(html) {

        if (html === false)
        {
            this.jqueryObj.find(".loader-footer").html("").hide();
        }

        if (typeof(html) == "string")
        {
            this.jqueryObj.find(".loader-footer").html(html).show();
        }

        return this;
    },

    appendHtmlInFooter : function(html)
    {
        this.jqueryObj.find(".loader-footer").append(html);

        return this;
    },

    addCssClass : function(cssclass)
    {
        this.jqueryObj.find(".loader-content").addClass(cssclass);

        return this;
    },

    show: function () {

        this.jqueryObj.show();

        return this;
    },

    hide : function()
    {
        this.setInfo(false);

        this.setFooter(false);

        this.jqueryObj.fadeOut();

        return this;
    },
    indicator : function(message, target, options)
    {
        var text = "";

        var defaultOptions = {
            target : {
                position : "bottom",
                cssClass : ""
            }
        };

        if (!options)
        {
            options = defaultOptions;
        }
        else
        {
            options = {...defaultOptions, ...options};
        }

        if (message !== false)
        {
            if (message)
            {
                text = message;
            }
            else
            {
                text = "Loading...";
            }
        }

        jqueryObj = $("#loadingIndicator");

        if (jqueryObj.length == 0)
        {
            var html = `
                <div id="loadingIndicator" class="indicator-loading-overlay">
                     <div class="indicator-loader-content">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span class="message">${text}</span>
                    </div>
                </div>
            `;

            $("body").append(html).addClass("indicator-applied");

            jqueryObj = $("#loadingIndicator");
        }
        else
        {
            jqueryObj.find(".message").html(text);
        }

        if (message === false)
        {
            jqueryObj.removeClass("active");
        }
        else if (text)
        {
            jqueryObj.addClass("active");

            if (target)
            {
                if ($(target).length <= 0)
                {
                    console.error("loader : target length is 0");
                }
                else if ($(target).length > 1)
                {
                    console.error("loader : target length is more than 1");
                }
                else
                {
                    const offset = $(target).offset();
                    

                    var targetTop = offset.top;
                    var targetLeft = offset.left;

                    var newPopperTop = targetTop; // Top of target + its height
                    var newPopperLeft = targetLeft; // Same left as target

                    if (options.target.position == "top")
                    {
                        newPopperTop -= 20;
                    }
                    else if (options.target.position == "top right")
                    {
                        newPopperTop -= 20;
                        newPopperLeft += $(target).outerWidth() - jqueryObj.outerWidth();
                    }
                    else if (options.target.position == "bottom")
                    {
                        newPopperTop += $(target).outerHeight();
                    }
                    else if (options.target.position == "bottom right")
                    {
                        newPopperTop += $(target).outerHeight();
                        newPopperLeft += $(target).outerWidth() - jqueryObj.outerWidth();
                    }
                    else if (options.target.position == "right")
                    {
                        newPopperLeft += $(target).outerWidth();
                    }
                    else if (options.target.position == "left")
                    {
                        newPopperLeft -= 160;
                    }
                    else if (options.target.position == "center")
                    {
                        newPopperTop = ($(target).outerHeight() / 2) - 20;
                        newPopperLeft = ($(target).outerWidth() / 2) - 160;
                    }
                     
                    // Set the position of the popper element
                    jqueryObj.css({
                        top: newPopperTop + 'px',
                        left: newPopperLeft + 'px'
                    });

                    if (options.target.cssClass)
                    {
                        jqueryObj.addClass(options.target.cssClass)
                    }
                }
            }
            else
            {
                jqueryObj.removeAttr("style");
                jqueryObj.attr("class", "indicator-loading-overlay active");
            }
        }

        return jqueryObj;
    }
};