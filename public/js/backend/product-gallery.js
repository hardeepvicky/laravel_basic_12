jQuery.fn.extend({
    browseProductGallery: function (obj) {
        if (typeof obj == "undefined") {
            obj = {};
        }

        if (typeof obj.postfix == "undefined") {
            obj.postfix = "main";
        }

        if (typeof obj.opener == "undefined") {
            alert("Please pass opener in argument");
            return;
        }

        return this.each(function () {
            var _this = $(this);

            $(this).hide();

            var value = $(this).val();
            var multiple = $(this).attr("multiple");
            multiple = multiple ? 1 : 0;

            var file_info = $(this).parent().find(".gallery-files-info");
            var file_preview = $(this).parent().find(".gallery-files-preview");

            if (file_info.length == 0) {
                var html =
                    "<div><span class='gallery-files-info' style='margin-left:5px;'></span><div>";
                $(this).parent().append(html);
            }

            var modal_id = "gallery-modal-" + obj.postfix;

            var html ='<div class="modal full-width-model fade" id="' + modal_id +'" tabindex="-1" aria-hidden="true" style="display: none;" >';
                html +='<div class="modal-dialog modal-fullscreen" style="height:90vh;">';
                    html += '<div class="modal-content">';
                        html += '<div class="modal-header">';
                            html += '<h5 class="modal-title">Product Gallery</h5>';
                            html += '<button type="button" class="btn-close" data-bs-dismiss="modal">';
                            html += "</button>";
                        html += "</div>";
                    html += '<div class="modal-body"> </div>';
                        html += '<div class="modal-footer">';
                            html += '<button class="btn btn-danger de-select">De-Select All</button>';
                            html += '<button class="btn btn-info select">Select</button>';
                        html += "</div>";
                    html += "</div>";
                html += "</div>";
            html += "</div>";

            console.log(modal_id);
            var gallery_modal = $("#" + modal_id);

            var list = value ? value.split(",") : [];
            file_info.html(list.length + " Products Selected");

            function preview_products(value)
            {
                if (value)
                {
                    file_preview.load("/product-preview?ids=" + value);
                }
                else
                {
                    file_preview.html("");
                }
            }

            preview_products(value);

            if (gallery_modal.length == 0) {
                $("body").append(html);
                gallery_modal = $("#" + modal_id);
            }

            gallery_modal
                .find(".modal-footer button.select")
                .click(function () {
                    var list = [];
                    gallery_modal.find(".file-block.active").each(function () {
                        var id = $(this).data("id");
                        list.push(id);
                    });

                    if (list.length == 0) {
                        bootbox.alert("Please select at least one Product");
                    } else {
                        file_info.html(list.length + " Products Selected");

                        preview_products(list.join(","));

                        _this.val(list.join(","));

                        gallery_modal.modal("hide");

                        $(".modal-backdrop").remove();

                        if (typeof obj.onSelect == "function") {
                            obj.onSelect(list);
                        }
                    }
                });

            gallery_modal
                .find(".modal-footer button.de-select")
                .click(function () {
                    $(".file-block").removeClass("active");
                    gallery_modal
                        .find(".modal-footer button.select")
                        .trigger("click");
                });

            $(obj.opener).click(function () {
                var request_data = {
                    value: value,
                    multiple: multiple,
                    pagination_limit: 10,
                    modal_id : modal_id,
                    full_view: true,
                };

                if (
                    typeof obj.product_category_id != "undefined" &&
                    obj.product_category_id
                ) {
                    request_data.product_category_id =
                        obj.product_category_id;
                }

                if (typeof obj.index_table_id != "undefined" && obj.index_table_id) {
                    request_data.index_table_id = obj.index_table_id;
                }

                var query_params = get_array_to_url(request_data);

                var url = "/product-gallery?" + query_params;

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
            $.events.onError("File Galley", "url not found in data-url");
        }
    },
});
