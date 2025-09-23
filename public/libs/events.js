$.events = {
<<<<<<< HEAD
    onAjaxError: function (title, responseHtml, extra) {
        var config = {
            title: title,
            message: responseHtml,
            size: "extra-large",
        };

        bootbox.alert(config);
=======
    lastCallbacks: [],
    execAtLast: function (callback) {
        this.lastCallbacks.push(callback);
    },
    onAjaxError: function (title, responseHtml, extra) {
        var config = {
            icon: "error",
            showCloseButton: true,
            html: title + "<br/>" + responseHtml,
        };

        if (config.html.length > 255) {
            config.width = "80%";
            config.customClass = {
                popup: "swal_error",
            };
        }

        Swal.fire(config);
>>>>>>> base_v3
    },
    onUserError: function (html, extra) {
        var config = {
            icon: "error",
            showCloseButton: true,
            html: html,
        };

        if (typeof extra == "object") {
            if (typeof extra.width == "string") {
                config.width = extra.width;
            }
        }

        Swal.fire(config);
    },
<<<<<<< HEAD
    onUserWarning : function(html, extra) {
=======
    onUserWarning: function (html, extra) {
>>>>>>> base_v3
        var config = {
            icon: "warning",
            showCloseButton: true,
            html: html,
        };

        if (typeof extra == "object") {
            if (typeof extra.width == "string") {
                config.width = extra.width;
            }
        }

        Swal.fire(config);
<<<<<<< HEAD
    }
};
=======
    },
    onError: function (title, html, extra) {
        var config = {
            icon: "error",
            showCloseButton: true,
            html: title + "<br/>" + html,
        };

        if (config.html.length > 255) {
            config.width = "80%";
            config.customClass = {
                popup: "swal_error",
            };
        }

        Swal.fire(config);
    },

    customErrors: function (title, custom_errors) {
        var html = '<ol class="mb-0" style="text-align: left;">';
        for (var group in custom_errors) 
        {
            if (!$.isNumeric(group)) {
                html += "<li>";
                html += `<b>${group}</b>`;
                html += "</li>";
            }

            html += "<ol>";

            for (var e in custom_errors[group]) 
            {
                var error = custom_errors[group][e];
                if (
                    typeof error["label"] == "string" &&
                    typeof error["msg"] == "string"
                ) {
                    html += "<li>";
                        if (error["label"]) {
                            html += error["label"] + " : ";
                        }

                        html += error["msg"];

                    html += "</li>";
                }
            }
            html += "</ol>";
        }

        console.log(html);

        $.events.onError(title, html)
    },
};
>>>>>>> base_v3
