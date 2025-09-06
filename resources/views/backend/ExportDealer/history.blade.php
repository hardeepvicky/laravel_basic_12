<?php

use App\Models\Lead;
use App\Models\LeadHistory;
use App\Helpers\DateUtility;

?>

@extends($layout)

@section('content')
    <?php
    $page_header_links = [
        ['title' => 'Edit', 'url' => route($routePrefix . '.edit', [$record])],
        ['title' => 'List', 'url' => route($routePrefix . '.index')],
    ];
    ?>

    @include($partial_path . '.page_header')

    @include($viewPrefix . '.show')

    <x-Backend.form-errors />

    <div class="card">
        <div class="card-header card-no-border pb-0">
            History

            <a class="btn btn-secondary call_link" href="tel:+{{ $record->country->phonecode }}{{ $record->mobile }}">Call</a>
            <div class="btn btn-secondary sms_link">SMS</div>
            <a class="btn btn-secondary whatsapp_link" href="https://web.whatsapp.com/" target="_blank" rel="noopener noreferrer">
                Whatsapp
            </a>
            <div class="btn btn-secondary email_link">Email</div>            
            <div class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modal-transfer-form">Transfer</div>
            <div class="card-header-right">
                <ul class="list-unstyled card-option" style="width: 230px;">
                    <li><i class="fa-solid fa-maximize full-card"></i></li>
                    <li><i class="fa-solid fa-minus minimize-card"></i></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered summary-table">
                    <thead>
                        <tr class="border-bottom-primary">
                            <th>Sr. No.</th>
                            <th style="width:10%">{{ get_value_from_list($history_field_labels, 'created_at', 'created_at') }}</th>
                            <th><?= get_value_from_list($history_field_labels, 'action_type', 'action_type') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'action', 'action') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'response_type_id', 'response_type_id') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'response', 'response') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'remarks', 'remarks') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'status_type', 'status_type') ?></th>
                            <th><?= get_value_from_list($history_field_labels, 'assign_to_user_id', 'assign_to_user_id') ?></th>
                            <th style="width:10%"><?= get_value_from_list($history_field_labels, 'next_followup_date_time', 'next_followup_date_time') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($histories as $i => $history)
                        <?php
                            $history->afterFind();
                        ?>
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <x-Backend.created-and-by :datetime="$history->created_at" :userId="$history->created_by" />
                            </td>
                            <td>{{ get_value_from_list($action_type_list, $history->action_type) }}</td>
                            <td>
                                <div class="more-less-text">{{ $history->action }}</div>
                            </td>
                            <td>
                                {{ get_value_from_list($history_response_list, $history->response_type_id) }}
                            </td>
                            <td>
                                <div class="more-less-text">{{ $history->response }}</div>
                            </td>
                            <td>
                                <div class="more-less-text">{{ $history->remarks }}</div>
                            </td>
                            <td>
                                {{ get_value_from_list($status_type_list, $history->status_type) }}
                            </td>
                            <td>
                                {{ get_value_from_list($userListCache, $history->assign_to_user_id) }}
                            </td>
                            <td>
                                <x-Backend.created-and-by :datetime="$history->next_meeting_date_time" userId="0" />
                            </td>
                        </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-sms-whatsapp-email-form" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5"></h3>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="height: 80vh; overflow-y:scroll">
                    <x-form.start action="{{ route('export-dealer.history_store', [$record->id]) }}" method="POST" />
                        <x-Inputs.text-field type="hidden" name="entry_by" :label="$false" value="MANUAL" />
                        <x-Inputs.text-field type="hidden" name="action_type" :label="$false" value="" class="action_type" />
                        <div class="form-group mb-3">
                            <?php
                            $field = 'action';
                            $label = get_value_from_list($history_field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-area :id="$field" :name="$field" :label="$label" rows="3" mandatory="true" />
                        </div>
                        <div class="form-group mb-3">
                            <?php
                            $field = 'response_type_id';
                            $label = get_value_from_list($history_field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                :list="$history_response_list" class="modal-select2"
                                />
                        </div>
                        <div class="form-group mb-3">
                            <?php
                            $field = 'response';
                            $label = get_value_from_list($history_field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-area :id="$field" :name="$field" :label="$label" rows="3"/>
                        </div>
                        <div class="form-group mb-3">
                            <?php
                            $field = 'status_type';
                            $label = get_value_from_list($history_field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                :list="$status_type_list" :value="$record->{$field}" class="modal-select2" mandatory="true"
                                />
                        </div>
                        <div class="form-group mb-3">
                            <?php
                            $field = 'remarks';
                            $label = get_value_from_list($history_field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-area :id="$field" :name="$field" :label="$label" rows="3"/>
                        </div>
                        <div class="form-group mb-3">
                            <?php
                            $field = 'next_followup_date_time';
                            $label = get_value_from_list($history_field_labels, $field, $field);
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$label" class="next-date-time-picker"/>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary closed" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary submit" type="button">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-transfer-form" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5">Tranfer To Another User</h3>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="height: 40vh; overflow-y:scroll">
                    <x-form.start action="{{ route('export-dealer.transfer', [$record->id]) }}" method="POST" />
                        <x-Inputs.text-field type="hidden" name="action_type" :label="$false" value="{{ LeadHistory::ACTION_TYPE_LEAD_TRANSFER }}" />
                        <div class="form-group mb-3">
                            <?php
                            $field = 'assign_to_user_id';
                            $label = get_value_from_list($history_field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                :list="$user_list" class="modal-select2"
                                mandatory="true" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary closed" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary submit" type="button">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function(){

            function modal_form_init(modal) {
                setTimeout(() => {
                    modal.find('.modal-select2').select2({
                        placeHolder: "Please Select",
                        theme: "bootstrap-5",
                        dropdownParent: modal
                    });
                }, 500);

                modal.find(".next-date-time-picker").flatpickr({
                    enableTime: true,
                    minDate: "today",
                    dateFormat: constants.dateTimeFormat,
                    time_24hr: true
                });

                modal.find(".template-table").srTableTemplate({
                    brace_type: "(",
                    afterRowAdd(_table, last_id, _tr) {
                        // _tr.find(".will-select2").select2({
                        //     placeHolder: "Please Select",
                        //     theme: "bootstrap-5",
                        //     dropdownParent: modal
                        // });

                        _tr.find(".will-required").attr("required", true);
                    }
                });

                var _trs = modal.find(".template-table").find("> tbody > tr").not(".sr-table-template-row");

                modal.find(".modal-body form").submit(function(){
                    var _this = $(this);
                    var confirm = _this.attr("data-confirm");

                    if (!confirm)
                    {
                        confirmDialog("", function(){
                            _this.attr("data-confirm", 1);
                            _this.trigger("submit");
                        })
                        return false;
                    }
                });

                modal.find(".modal-body form").ajaxForm({
                    beforeSubmit: function(arr, $form, options) {

                        $.loader.init().setInfo("Uploading....").show();

                        return true;
                    },
                    uploadProgress: function(event, position, total, percentComplete) {
                        $.loader.setInfo("Uploading " + percentComplete + "%");
                    },
                    error: function(xhr, status, errorString) {
                        $.loader.init().hide();

                        if (typeof xhr.responseText == "string")
                        {
                            response = xhr.responseText.trim();

                            if (response.length == 0) {
                                $.events.onAjaxError("JSON Parse Error", "Empty Response");
                                return false;
                            }
                        }
                        else
                        {
                            console.log(xhr.responseText);
                            return false;
                        }

                        try {
                            var responseJson = JSON.parse(response);
                        } catch (e) {
                            $.events.onAjaxError("JSON Parse Error", response);
                            return false;
                        }

                        var first_error_ele = form_errors(modal.find("form"), responseJson);
                        var modalBody = modal.find('.modal-body');
                        if (modalBody.length > 0 && first_error_ele)
                        {
                            modalBody.animate({
                                scrollTop: first_error_ele.offset().top - modalBody.offset().top + modalBody.scrollTop() - 200
                            }, 800);
                        }
                    },
                    success: function(responseText, statusText, xhr) {

                        var form_url = modal.find("form").attr("action");

                        ajaxHandleResponse(form_url, responseText, function(response) {
                            modal.find("button.closed").trigger("click");

                            $.loader.init().setInfo("Reloading....");
                            window.location.reload(true);
                        });
                    }
                });
            }

            modal_form_init($("#modal-transfer-form"));

            $('#modal-transfer-form .submit').click(function() {
                $('#modal-transfer-form form').trigger("submit");
            });


            modal_form_init($("#modal-sms-whatsapp-email-form"));

            $('#modal-sms-whatsapp-email-form .submit').click(function() {
                $('#modal-sms-whatsapp-email-form form').trigger("submit");
            });

            $(".sms_link").click(function(){
                var modal = $("#modal-sms-whatsapp-email-form");

                modal.find("form").find('input:text, textarea').val('');

                modal.find(".modal-title").text("SMS");
                modal.find(".action_type").val('{{ LeadHistory::ACTION_TYPE_SEND_SMS }}');

                modal.modal("show");
            });

            $(".call_link").click(function(){
                var modal = $("#modal-sms-whatsapp-email-form");

                modal.find("form").find('input:text, textarea').val('');

                modal.find(".modal-title").text("CALL");
                modal.find(".action_type").val('{{ LeadHistory::ACTION_TYPE_CALL }}');

                modal.modal("show");
            });

            $(".whatsapp_link").click(function(){
                var modal = $("#modal-sms-whatsapp-email-form");

                modal.find("form").find('input:text, textarea').val('');

                modal.find(".modal-title").text("Whatsapp");
                modal.find(".action_type").val('{{ LeadHistory::ACTION_TYPE_SEND_WHATSAPP }}');

                modal.modal("show");
            });

            $(".email_link").click(function(){
                var modal = $("#modal-sms-whatsapp-email-form");

                modal.find("form").find('input:text, textarea, select').val('').find('input:checkbox, input:radio').prop('checked', false);

                modal.find(".modal-title").text("Email");
                modal.find(".action_type").val('{{ LeadHistory::ACTION_TYPE_SEND_EMAIL }}');

                modal.modal("show");
            });

            $(".more-less-text").srParagraph();
        });
    </script>
@endsection
