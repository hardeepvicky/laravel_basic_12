jQuery.fn.extend({
    browseContact: function (obj) {
        if (typeof obj == "undefined") {
            obj = {};
        }

        if (typeof obj.postfix == "undefined") {
            obj.postfix = "main";
        }

        if (typeof obj.selectedValues == "undefined") {
            alert("Please pass selectedValues in argument");
            return;
        }

        if (typeof obj.onSelect != "function") {
            alert("Please pass function onSelect() in argument");
            return;
        }

        if (typeof obj.multiple == "undefined") {
            alert("Please pass multiple in argument");
            return;
        }

        return this.each(function () {
            var _this = $(this);

            var modal_id = "gallery-modal-" + obj.postfix;

            var html ='<div class="modal full-width-model fade" id="' + modal_id +'" tabindex="-1" aria-hidden="true" style="display: none;" >';
                html +='<div class="modal-dialog modal-fullscreen" style="height:90vh;">';
                    html += '<div class="modal-content">';
                        html += '<div class="modal-header">';
                            html += '<h5 class="modal-title">Contacts</h5>';
                            html += '<button type="button" class="btn-close" data-bs-dismiss="modal">';
                            html += "</button>";
                        html += "</div>";
                    html += '<div class="modal-body"> </div>';
                        html += '<div class="modal-footer">';
                            html += '<button class="btn btn-info select">Done</button>';
                        html += "</div>";
                    html += "</div>";
                html += "</div>";
            html += "</div>";

            console.log(modal_id);
            var gallery_modal = $("#" + modal_id);

            if (gallery_modal.length == 0) {
                $("body").append(html);
                gallery_modal = $("#" + modal_id);
            }

            gallery_modal
                .find(".modal-footer button.select")
                .click(function () {
                    var list = [];
                    gallery_modal.find("input.contact:checked").each(function () {
                        var type = $(this).data("type");
                        var id = $(this).val();
                        list.push({
                            "type" : type,
                            "id" : id
                        });
                    });

                    if (list.length == 0) {
                        bootbox.alert("Please select at least one Contact");
                    } else {

                        gallery_modal.modal("hide");

                        obj.onSelect(list);
                    }
                });

            $(this).click(function () 
            {
                var request_data = {
                    value: obj.selectedValues,
                    multiple: obj.multiple,
                    pagination_limit: 100,
                    modal_id : modal_id,
                    full_view: true,
                };

                if (
                    typeof obj.type != "undefined" && obj.type
                ) {
                    request_data.type = obj.type;
                }

                if (typeof obj.index_table_id != "undefined" && obj.index_table_id) {
                    request_data.index_table_id = obj.index_table_id;
                }

                var query_params = get_array_to_url(request_data);

                var url = "/contact-chooser?" + query_params;

                console.log(url);

                $(gallery_modal).attr("data-url", url);
                $(gallery_modal).refresh();
                $(gallery_modal).modal("show");
            });
        });
    },
    refresh: function (cb) {
        var url = $(this).attr("data-url");

        if (url) {
            $(this).find(".modal-body").html("Loading...");
            $(this)
                .find(".modal-body")
                .load(url, function () {
                    if (typeof cb == "function") {
                        cb();
                    }
                });
        } else {
            $.events.onError("Galley", "url not found in data-url");
        }
    },
});
