@extends('auth.layouts.login')
@section('content')
    <div id="otp_send_choose" style="width : 100%; max-width : 500px;">
        <div class="d-flex flex-column h-100">
            <div class="auth-content my-auto">
                <div class="text-center">
                    <x-Backend.session-flash />

                    <div class="list-container">
                        <h4 class="mb-2 p-2">Click Below Button to send OTP</h4>
                        <ul class="custom-list-group mb-3" id="otp_via_list">
                            <li class="custom-list-group-item" id="otp_via_email">
                                <span class="item-text">Email</span>
                                <i class="fa-solid fa-envelope item-icon"></i>
                            </li>
                            <li class="custom-list-group-item" id="otp_via_whatsapp">
                                <span class="item-text">Whatsapp</span>
                                <i class="fa-brands fa-whatsapp item-icon"></i>
                            </li>
                        </ul>

                        <div id="alert_info"
                            class="alert text-info border-info outline-2x alert-dismissible fade show alert-icons mb-2"
                            role="alert">
                            <i class="fa-solid fa-circle-info"> </i>
                            <p class="mb-0"><b>Info! </b>
                                <span class="message">Sending OTP... </span>
                            </p>
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>

                        <form id="verify" class="mb-2">
                            <h4 class="mb-2 p-2 b-t-primary">Enter OTP</h4>
                            <div class="row">
                                <div class="col-2">
                                    <div class="mb-3">
                                        <label for="digit1-input" class="visually-hidden">Dight 1</label>
                                        <input type="text" class="form-control form-control-lg text-center two-step"
                                            maxlength="1" data-value="1" id="digit1-input">
                                    </div>
                                </div>

                                <div class="col-2">
                                    <div class="mb-3">
                                        <label for="digit2-input" class="visually-hidden">Dight 2</label>
                                        <input type="text" class="form-control form-control-lg text-center two-step"
                                            maxlength="1" data-value="2" id="digit2-input">
                                    </div>
                                </div>

                                <div class="col-2">
                                    <div class="mb-3">
                                        <label for="digit3-input" class="visually-hidden">Dight 3</label>
                                        <input type="text" class="form-control form-control-lg text-center two-step"
                                            maxlength="1" data-value="3" id="digit3-input">
                                    </div>
                                </div>

                                <div class="col-2">
                                    <div class="mb-3">
                                        <label for="digit4-input" class="visually-hidden">Dight 4</label>
                                        <input type="text" class="form-control form-control-lg text-center two-step"
                                            maxlength="1" data-value="4" id="digit4-input">
                                    </div>
                                </div>

                                <div class="col-2">
                                    <div class="mb-3">
                                        <label for="digit5-input" class="visually-hidden">Dight 5</label>
                                        <input type="text" class="form-control form-control-lg text-center two-step"
                                            maxlength="1" data-value="4" id="digit5-input">
                                    </div>
                                </div>

                                <div class="col-2">
                                    <div class="mb-3">
                                        <label for="digit6-input" class="visually-hidden">Dight 6</label>
                                        <input type="text" class="form-control form-control-lg text-center two-step"
                                            maxlength="1" data-value="4" id="digit6-input">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button class="btn btn-primary w-100 waves-effect waves-light"
                                    type="submit">Verify</button>
                            </div>
                        </form>

                        <div style="text-align:left;">
                            <p class="text-muted mb-2">
                                OTP vaild for 10 Mintues Only
                            </p>

                            <p id="wait-time-block" class="text-muted">
                            </p>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        <script src="/assets/js/two-step-verification.init.js"></script>

        <script>
            $(function() {
                var otp_sent_by_backend = parseBool('{{ request("otp_sent", false) }}');
                var user = JSON.parse('<?= json_encode($user) ?>');
                var send_via = '';

                $("#alert_info").hide();
                $("#wait-time-block").hide();

                function on_send_otp() {

                    $.sr.wait(60, function(s) {

                        $("#wait-time-block").html("if you did not receive otp then you can send again in " + s + " seconds");

                        if (s == 60) {
                            $("#wait-time-block").show();
                        }

                    }, function() {

                        $("#wait-time-block").hide();
                    });
                }

                $("#otp_via_email").click(function() {

                    if (!$("#wait-time-block").is(":hidden")) {
                        $.events.onUserWarning($("#wait-time-block").html());
                        return;
                    }

                    $("#otp_via_list .custom-list-group-item").removeClass("selected");
                    $(this).addClass("selected");

                    send_via = 'email';

                    send_email_user_otp(user, function(response) {
                        $("#alert_info .message").html(response['msg']);
                        $("#alert_info").show();

                        on_send_otp();
                    });
                });

                $("#otp_via_whatsapp").click(function() {

                    if (!$("#wait-time-block").is(":hidden")) {
                        $.events.onUserWarning($("#wait-time-block").html());
                        return;
                    }

                    $("#otp_via_list .custom-list-group-item").removeClass("selected");
                    $(this).addClass("selected");

                    send_via = 'whatsapp';

                    send_whatsapp_user_otp(user, function(response) {
                        $("#alert_info .message").html(response['msg']);
                        $("#alert_info").show();

                        on_send_otp();
                    });
                });

                if (otp_sent_by_backend)
                {
                    on_send_otp();
                }


                $("#verify").submit(function(e) {

                    e.preventDefault();

                    var list = [];
                    var result = true;
                    $("input.two-step").each(function() {
                        var v = $(this).val().trim();
                        if (v) {
                            list.push(v);
                        } else {
                            result = false;
                        }
                    });

                    if (!result) {
                        Swal.fire({
                            icon: "error",
                            showCloseButton: true,
                            html: "Please Enter all Digits",
                        });

                        return false;
                    }

                    var request = {
                        user_id: user.id,
                        otp: list.join(""),
                        timezone: getTimeZone()
                    };

                    ajaxPostJson("/ajax_otp_verify", request, function(response) {

                        Swal.fire({
                            icon: "success",
                            showCloseButton: true,
                            html: "OTP Verify Successfully",
                        });

                        window.location.href = response.redirect_to;
                    });

                    return false;
                });



                $("#ajax_re_send_email").click(function() {});
            });
        </script>
    @endsection
