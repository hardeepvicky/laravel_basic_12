$.events = {
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

        Swal.fire(config);
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
    onUserWarning: function (html, extra) {
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
    },
    onError: function (title, html, extra) {
        var config = {
            icon: "error",
            showCloseButton: true,
            html: title + "<br/>" + html,
        };

        Swal.fire(config);
    },
};
