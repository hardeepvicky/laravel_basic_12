function niceBytes(bytes, i) {
<<<<<<< HEAD
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

function niceNumbers($number, $count = 0)
{
	if ($number > 1000) {
		return niceNumbers(Math.round($number / 1000), $count + 1);
	}

	$types = ["", "K", "M", "T"];

	return $number + " " + $types[$count];
}

function ajaxHandleResponse(url, response, callback) {

=======
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
>>>>>>> base_v3
    var responseJson = {};
    if (typeof response == "object") {
        responseJson = response;
    } else {
        try {
<<<<<<< HEAD

            if (typeof (response) == "string") {
=======
            if (typeof response == "string") {
>>>>>>> base_v3
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
<<<<<<< HEAD
=======
            $.loader.hide();
>>>>>>> base_v3
            $.events.onAjaxError("JSON Parse Error", response, {
                url: url,
            });
            return false;
        }
    }

    if (typeof responseJson["status"] == "undefined") {
<<<<<<< HEAD
=======
        $.loader.hide();
>>>>>>> base_v3
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
<<<<<<< HEAD
        $.events.onUserError(responseJson["msg"]);
    } else {
=======
        $.loader.hide();
        
        if (typeof responseJson['custom_errors'] == "object")
        {
            $.events.customErrors(responseJson["msg"], responseJson['custom_errors']);
        }
        else
        {
            $.events.onUserError(responseJson["msg"]);
        }
    } else {
        $.loader.hide();
>>>>>>> base_v3
        $.events.onAjaxError("Missing", "Response JSON Should have msg", {
            url: url,
        });
    }
<<<<<<< HEAD
}



function ajaxGetJson(url, callback) {
    $.loader.init();
    $.loader.setInfo("Loading...").show();

    $.get(url, function (response) {

        $.loader.hide();

        ajaxHandleResponse(url, response, callback);

=======

    return responseJson;
}

function ajaxGetJson(url, callback) {
    $.loader.init();
    $.loader.setInfo("Sending Request...").show();

    $.get(url, function (response) {
        $.loader.hide();

        ajaxHandleResponse(url, response, callback);
>>>>>>> base_v3
    }).fail(function (xhr, status, title) {
        $.loader.hide();
    });
}

function ajaxPostJson(url, data, callback) {
    $.loader.init();
<<<<<<< HEAD
    $.loader.setInfo("Loading...").show();

    $.post(url, data, function (response) {

        $.loader.hide();

        ajaxHandleResponse(url, response, callback);

=======
    $.loader.setInfo("Sending Request...").show();

    $.post(url, data, function (response) {
        $.loader.hide();

        ajaxHandleResponse(url, response, callback);
>>>>>>> base_v3
    }).fail(function (xhr, status, title) {
        $.loader.hide();
    });
}

<<<<<<< HEAD

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
                input.parent().append('<span class="error-message">' + errs[e] + '<span>');
            }
        }

        if (select.length > 0) {
            error_input_found = true;
            for (var e in errs) {
                select.parent().append('<span class="error-message">' + errs[e] + '<span>');
            }
        }

    }

    return error_input_found;
=======
function form_errors(form, responseJson) {

    form.find(".error-message").remove();

    var first_error_msg = null;
    var any_field_found = false;

    if (typeof responseJson['errors'] == "object")
    {
        for (var field_name in responseJson['errors'])
        {
            var errors = responseJson['errors'][field_name];

            var field = form.find("[name='"+ field_name +"']");

            var html = "";

            for (var a in errors)
            {
                html += '<span class="error-message">' + errors[a] + '</span>';
            }

            if (field.length > 0)
            {
                any_field_found = true;
                field.parent().append(html);
            }
            else
            {
                first_error_msg = html;
            }
        }
    }
    else if (typeof responseJson['message'] == "string")
    {
        $.events.onUserError(responseJson['message']);
    }

    if (any_field_found)
    {
        return form.find(".error-message").first();
    }
    else
    {
        $.events.onUserError(first_error_msg);
    }
>>>>>>> base_v3
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

<<<<<<< HEAD
function form_input_toggle_mandatory(element, is_show)
{
    if (is_show)
    {
        if ( element.closest(".form-group").find(".form-label .mandatory").length == 0)
        {
            element.closest(".form-group").find(".form-label").append('<span class="mandatory">*</span>');
        }
        else
        {
            element.closest(".form-group").find(".form-label .mandatory").show();
        }

        element.attr("required", "true");
    }
    else
    {
=======
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
>>>>>>> base_v3
        element.closest(".form-group").find(".form-label .mandatory").hide();
        element.removeAttr("required");
    }
}

<<<<<<< HEAD
function form_check_unique_list(cls)
{
    var result = true;
    var list = [];

    $(cls).each(function(){
        var v = $(this).val();
        if (v)
        {
            if (list.includes(v))
            {
=======
function form_check_unique_list(cls) {
    var result = true;
    var list = [];

    $(cls).each(function () {
        var v = $(this).val();
        if (v) {
            if (list.includes(v)) {
>>>>>>> base_v3
                result = false;
            }

            list.push(v);
        }
    });

    // console.log({"form_check_unique_list" : list});

    return result;
}

function str_convert_space_to_hyphine(str) {
<<<<<<< HEAD
    return str.replace(/\s+/g, '-');  // Replace multiple spaces with a single hyphen
}

function str_trim_hyphine(str) {
    return str.replace(/^-+|-+$/g, ''); 
=======
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

function send_whatsapp_user_otp(user, callback) {
    ajaxGetJson("/send_whatsapp_user_otp/" + user.uid, function (response) {
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
        if (item.name == "_token")
        {

        }
        else
        {
            item.value = encodeURIComponent(item.value);
        }

        console.log(item);

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

function getBasenameFromPath(path) {
    // Find the last occurrence of '/' or '\'
    const lastSlashIndex = Math.max(path.lastIndexOf('/'), path.lastIndexOf('\\'));

    // If a slash is found, return the substring after it
    if (lastSlashIndex !== -1) {
        return path.substring(lastSlashIndex + 1);
    }

    // If no slash is found, the whole path is likely the filename
    return path;
}

function getFilenameFromPath(path)
{
    path = getBasenameFromPath(path);

    const lastIndex = path.lastIndexOf('.');

    // If a slash is found, return the substring after it
    if (lastIndex !== -1) {
        return path.substring(0, lastIndex);
    }

    return path;
}

function get_array_to_url(data)
{
    var list = [];

    for(var i in data)
    {
        i = encodeURIComponent(i);
        list.push(i + "=" + encodeURIComponent(data[i]));
    }

    return list.join("&");
}

function parseBool(value) {
    if (typeof value === 'boolean') {
        return value; // Already a boolean
    }
    if (typeof value === 'string') {
        value = value.toLowerCase().trim(); // Case-insensitive and trim whitespace
        if (value === 'true' || value === '1' || value === 'yes') {
            return true;
        }
        if (value === 'false' || value === '0' || value === 'no') {
            return false;
        }
    }
    return null;
}

function get_other_country_date_time(datetime, timezone, callback)
{
    $.post("/get_date_time_for_timezone", {datetime : DateUtil.getFormattedDateTime(datetime, 'Y-M-d H:i:s'), timezone}, function(response){
        callback(response);
    });
>>>>>>> base_v3
}