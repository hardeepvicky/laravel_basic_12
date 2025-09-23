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

<<<<<<< HEAD
    init: function () {
        this.jqueryObj = $("body .loader-container");
=======
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
>>>>>>> base_v3

        if (this.jqueryObj.length > 0) {
            return this;
        }

        var html = '<div class="loader-container" style="display:none">';
            html += '<div class="loader-content">';
<<<<<<< HEAD
                html += '<div class="loader-icon spinner-border"></div>';        
                html += '<div class="loader-info" style="display:none"></div>';                
                html += '<div class="loader-footer" style="display:none"></div>';        
            html += "</div>";
        html += "</div>";

        $("body").append(html).addClass("loader-applied");
        
        this.jqueryObj = $("body .loader-container");

        return this;
    },
   
=======
                html += '<div class="loader-icon spinner-border"></div>';
                html += '<div class="loader-info" style="display:none"></div>';
                html += '<div class="loader-footer" style="display:none"></div>';
            html += "</div>";
        html += "</div>";

        $(selector).append(html).addClass("loader-applied");

        this.jqueryObj = $(selector).find(".loader-container");

        return this;
    },

>>>>>>> base_v3
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
<<<<<<< HEAD
        
=======

>>>>>>> base_v3
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
<<<<<<< HEAD
        
=======

>>>>>>> base_v3
        this.jqueryObj.show();

        return this;
    },

    hide : function()
    {
        this.setInfo(false);

        this.setFooter(false);

        this.jqueryObj.fadeOut();

        return this;
<<<<<<< HEAD
=======
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

        var parent = target ? $(target) : $("body");

        jqueryObj = parent.find(".loadingIndicator");

        if (jqueryObj.length == 0)
        {
            var html = `
                <div class="loadingIndicator indicator-loading-overlay">
                     <div class="indicator-loader-content">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span class="message">${text}</span>
                    </div>
                </div>
            `;

            parent.append(html).addClass("indicator-applied");

            jqueryObj = parent.find(".loadingIndicator");
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
                    var newPopperTop = 0; // Top of target + its height
                    var newPopperLeft = 0; // Same left as target

                    var position = options.target.position.toLowerCase();

                    if (position.includes("right"))
                    {
                        newPopperLeft += $(target).outerWidth() - jqueryObj.outerWidth();
                    }

                    if (position.includes("bottom"))
                    {
                        newPopperTop += $(target).outerHeight() - 20;
                    }

                    if (position == "center")
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
>>>>>>> base_v3
    }
};