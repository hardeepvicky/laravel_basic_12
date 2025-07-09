function niceBytes(bytes, i) {
    var list = ["B", "KB", "MB", "GB", "TB"];

    if (typeof i == "undefined") {
        i = 0;
    }

    var temp = bytes / 1024;

    if (temp > 1024) {
        return niceBytes(temp, i + 1);
    }

    if (temp < 1) {
        return bytes.toFixed(1) + " " + list[i];
    } else {
        return temp.toFixed(1) + " " + list[i + 1];
    }
}

function niceNumbers($number, $count = 0) {
    if ($number > 1000) {
        return niceNumbers(Math.round($number / 1000), $count + 1);
    }

    $types = ["", "K", "M", "T"];

    return $number + " " + $types[$count];
}

function ajaxHandleResponse(url, response, callback) {
    var responseJson = {};
    if (typeof response == "object") {
        responseJson = response;
    } else {
        try {
            if (typeof response == "string") {
                response = response.trim();

                if (response.length == 0) {
                    $.events.onAjaxError("JSON Parse Error", "Empty Response", {
                        url: url,
                    });

                    return false;
                }

                var responseJson = JSON.parse(response);
            }
        } catch (e) {
            $.events.onAjaxError("JSON Parse Error", response, {
                url: url,
            });
            return false;
        }
    }

    if (typeof responseJson["status"] == "undefined") {
        $.events.onAjaxError("Missing", "Response JSON Should have status", {
            url: url,
        });
        return;
    }

    if (responseJson["status"] == "1" || responseJson["status"] == true) {
        if (typeof callback == "function") {
            callback(responseJson);
        }
    } else if (typeof responseJson["msg"] != "undefined") {
        $.events.onUserError(responseJson["msg"]);
    } else {
        $.events.onAjaxError("Missing", "Response JSON Should have msg", {
            url: url,
        });
    }

    return responseJson;
}

function ajaxGetJson(url, callback) {
    $.loader.init();
    $.loader.setInfo("Loading...").show();

    $.get(url, function (response) {
        $.loader.hide();

        ajaxHandleResponse(url, response, callback);
    }).fail(function (xhr, status, title) {
        $.loader.hide();
    });
}

function ajaxPostJson(url, data, callback) {
    $.loader.init();
    $.loader.setInfo("Loading...").show();

    $.post(url, data, function (response) {
        $.loader.hide();

        ajaxHandleResponse(url, response, callback);
    }).fail(function (xhr, status, title) {
        $.loader.hide();
    });
}

function form_errors(form, errors) {
    var error_input_found = false;

    form.find(".error-message").remove();

    for (var field in errors) {
        var errs = errors[field];
        var key = "[name='" + field + "']";
        var input = form.find("input" + key);
        var select = form.find("select" + key);

        if (input.length > 0) {
            error_input_found = true;
            for (var e in errs) {
                input
                    .parent()
                    .append(
                        '<span class="error-message">' + errs[e] + "<span>"
                    );
            }
        }

        if (select.length > 0) {
            error_input_found = true;
            for (var e in errs) {
                select
                    .parent()
                    .append(
                        '<span class="error-message">' + errs[e] + "<span>"
                    );
            }
        }
    }

    return error_input_found;
}

function confirmDialog(text, onYes) {
    Swal.fire({
        title: "Are you sure?",
        text: text,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: constants.swal.button.confirm_color,
        cancelButtonColor: constants.swal.button.cancel_color,
    }).then(function (e) {
        if (e.value) {
            if (typeof onYes == "function") {
                onYes();
            }
        }
    });
}

function form_input_toggle_mandatory(element, is_show) {
    if (is_show) {
        if (
            element.closest(".form-group").find(".form-label .mandatory")
                .length == 0
        ) {
            element
                .closest(".form-group")
                .find(".form-label")
                .append('<span class="mandatory">*</span>');
        } else {
            element
                .closest(".form-group")
                .find(".form-label .mandatory")
                .show();
        }

        element.attr("required", "true");
    } else {
        element.closest(".form-group").find(".form-label .mandatory").hide();
        element.removeAttr("required");
    }
}

function form_check_unique_list(cls) {
    var result = true;
    var list = [];

    $(cls).each(function () {
        var v = $(this).val();
        if (v) {
            if (list.includes(v)) {
                result = false;
            }

            list.push(v);
        }
    });

    // console.log({"form_check_unique_list" : list});

    return result;
}

function str_convert_space_to_hyphine(str) {
    return str.replace(/\s+/g, "-"); // Replace multiple spaces with a single hyphen
}

function str_trim_hyphine(str) {
    return str.replace(/^-+|-+$/g, "");
}

function send_email_user_otp(user, callback) {
    ajaxGetJson("/send_email_user_otp/" + user.uid, function (response) {
        if (typeof callback == "function") {
            callback(response);
        }
    });
}

function send_whatsapp_user_otp(user_id, callback) {
    ajaxGetJson("/send_whatsapp_user_otp/" + user_id, function (response) {
        if (typeof callback == "function") {
            callback(response);
        }
    });
}

function add_form_to_body(method, action, params) {
    const $form = $("<form>", {
        action: action,
        method: method,
        target: "_self", // Target self to navigate if not using AJAX, or a hidden iframe
    }).hide(); // Hide the form as it's for programmatic use

    // 2. Add input fields (hidden inputs for parameters)
    $.each(params, function (index, item) {
        item.value = encodeURIComponent(item.value);

        $("<input>")
            .attr({
                type: "hidden", // Hidden inputs are perfect for parameters
                name: item.name,
                value: item.value,
            })
            .appendTo($form);
    });

    $("body").append($form);

    return $form;
}

function add_form_to_body_and_submit(method, action, params) {
    const $form = add_form_to_body(method, action, params);

    $form.submit();

    setTimeout(function () {
        $form.remove();
        $("#responseArea").removeClass("hidden"); // Show a message to the user
    }, 100);
}

function getTimeZone() {
    var userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    if (userTimeZone == "Asia/Calcutta") {
        userTimeZone = "Asia/Kolkata";
    }

    return userTimeZone;
}

function get_celec_date_time(date) {
    if (!date) {
        date = new Date();
    }

    var divider = $('<div class="divider"></div>');

    var date_div = $('<div class="label">');
    date_div.html(DateUtil.getFormattedDateTime(date, "d-M-Y"));

    var time = DateUtil.getFormattedDateTime(date, "D H:i");

    console.log(time);

    var time_div = $('<div class="label">');
    time_div.html(time);

    var div = $("<div>");
    div.addClass("celec_info_inline");
    div.append(date_div);
    div.append(divider);
    div.append(time_div);

    return div;
}

function showProgressToast() {
    // Create a unique ID for the toast
    const toastId = "progressToast-" + Date.now();

    // Create the toast HTML dynamically
    const toastHtml = `
            <div class="toast progress-toast" role="alert" aria-live="assertive" aria-atomic="true" id="${toastId}">
                <div class="toast-header">
                    <strong class="me-auto">Processing Data</strong>
                    <small>...</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    <p id="${toastId}-message">Initializing process...</p>
                    <div class="progress mt-2">
                        <div id="${toastId}-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        `;

    // Append the toast HTML to the container
    toastContainer.insertAdjacentHTML("beforeend", toastHtml);

    // Get references to the specific toast elements
    const toastElement = document.getElementById(toastId);
    const progressBar = document.getElementById(`${toastId}-bar`);
    const progressMessage = document.getElementById(`${toastId}-message`);
    const toastHeaderSmall = toastElement.querySelector(".toast-header small");

    // Initialize the Bootstrap Toast
    const bsToast = new bootstrap.Toast(toastElement);

    // Show the toast
    bsToast.show();

    let progress = 0;
    let interval;

    // Function to update progress
    function updateProgress() {
        progress += 10;
        if (progress <= 100) {
            progressBar.style.width = progress + "%";
            progressBar.setAttribute("aria-valuenow", progress);
            progressMessage.textContent = `Processing: ${progress}% complete`;
            toastHeaderSmall.textContent = `${progress}%`;
        }

        if (progress >= 100) {
            clearInterval(interval);
            progressBar.classList.remove("progress-bar-animated"); // Stop animation
            progressBar.classList.remove("progress-bar-striped");
            progressBar.classList.add("bg-success"); // Change color to indicate completion
            progressMessage.textContent = "Process Complete!";
            toastHeaderSmall.textContent = `Finished`;

            // Optionally, auto-hide the toast after a short delay
            setTimeout(() => {
                bsToast.hide();
                // Remove the toast element from the DOM after it's hidden
                toastElement.addEventListener("hidden.bs.toast", function () {
                    toastElement.remove();
                });
            }, 2000); // Hide after 2 seconds
        }
    }

    // Simulate progress over time
    interval = setInterval(updateProgress, 500); // Update every 0.5 seconds
}
