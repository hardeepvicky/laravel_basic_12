/**
 * @author : Hardeep
 * this file only for backend
 */

$(document).ready(function () {
    $.loader.init();

    $("form")
        .find("div.pristine-error")
        .parents(".form-group")
        .addClass("has-danger");

    $("input[type='checkbox'].chk-select-all").chkSelectAll();

    $(".css-toggler").cssClassToggle();

    $(".ajax-load").ajaxLoad();

    $(".table-export-csv").srTableCSVExport();

    $(".select2").select2({
        placeHolder: "Please Select",
        theme: "bootstrap-5",
    });

    $(".fancybox").fancybox();

    $(".date-picker").flatpickr({
        dateFormat: constants.dateFormat
    });

    $(".date-time-picker").flatpickr({
        enableTime: true,        
        dateFormat: constants.dateTimeFormat
    });

    $(".time-picker").flatpickr({
        noCalendar: true,
        enableTime: true,
        dateFormat: constants.timeFormat,
    });

    // flatpickr(".date-month-picker", {
    //     disableMobile: "true",
    //     plugins: [
    //         new monthSelectPlugin({
    //             shorthand: true,
    //             dateFormat: "m/Y",
    //             altFormat: "F Y",
    //             theme: "material_blue"
    //         })
    //     ]
    // });

    $(".i-data-table").idataTable();

    $(".cascade-drop-down").cascade({
        beforeGet: function (src, url) {
            $.loader.init().setInfo("Fetching...").show();
            return url;
        },
        afterGet: function (src, dest, response) {
            return response;
        },
    });

    $(".sr-paragraph").srParagraph();

    /** -------------------------------------------------------- */

    $("input.invalid-char").on("keypress", function (event) {
        var regex = new RegExp("^[a-zA-Z0-9~!@#$%^*()<>{}_.,/+-\\\\]+$");
        var key = String.fromCharCode(
            !event.charCode ? event.which : event.charCode
        );
        key = key.trim();

        var error_span = $(this).parent().find(".error-message");
        if (error_span.length == 0) {
            $(this)
                .parent()
                .append(
                    '<span class="error-message">&#9679 Invalid Character</span>'
                );
            var error_span = $(this).parent().find(".error-message");
        }

        if (key && !regex.test(key)) {
            event.preventDefault();
            error_span.show();
            return false;
        }

        error_span.hide();
    });

    $(document).on("submit", "form.summary-delete-form", function () {
        var _form = $(this);

        var is_confirm = _form.attr("data-confirm");

        if (!is_confirm) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: constants.swal.button.confirm_color,
                cancelButtonColor: constants.swal.button.cancel_color,
                confirmButtonText: "Yes, delete it!",
            }).then(function (e) {
                if (e.value) {
                    _form.attr("data-confirm", 1);
                    _form.trigger("submit");
                }
            });

            return false;
        }
    });

    $(document).on("submit", "form.summary_search", function () {
        $(this).ajaxSubmit({
            target: "#index_table",
            beforeSubmit: function (formData, jqForm, options) {
                $.loader.show();
            },
            success: function (responseText, statusText, xhr, $form) {
                $.loader.hide();
            },
            error: function () {
                $.loader.hide();
            },
        });

        return false;
    });

    $(document).on("click", "form .clear_form_search_conditions", function () {
        var _form = $(this).closest("form");

        _form.clearForm();

        _form.find("input[name='is_sort_clear']").val(1);

        var v = _form
            .find(".pagination_limit:first")
            .find("option:first")
            .val();
        _form.find(".pagination_limit").val(v);

        _form.trigger("submit");
    });

    $(document).on("change", "form.summary_search select.pagination_limit", function () {
            var _form = $(this).closest("form");
            _form.trigger("submit");
        }
    );

    $(document).on("click", "#index_table .pagination a.page-link, #index_table a.sortable", function(){
            var href = $(this).attr("href");
            $.loader.show();

            $("#index_table").load(href, function () {
                $.loader.hide();
            });

            return false;
        }
    );

    $(document).on("click", "a.activate", function () {
        var _this = $(this).parent();
        var href = $(this).attr("href");

        ajaxGetJson(href, function (response) {
            var html = '<span class="badge bg-success">Active</span>';
            html += "<br/>";
            html +=
                '<a class="de_activate" href="' +
                response["url"] +
                '">De-Activate</a>';
            _this.html(html);
        });

        return false;
    });

    $(document).on("click", "a.de_activate", function () {
        var _this = $(this).parent();
        var href = $(this).attr("href");

        ajaxGetJson(href, function (response) {
            var html = '<span class="badge bg-danger">De-Active</span>';
            html += "<br/>";
            html +=
                '<a class="activate" href="' +
                response["url"] +
                '">Activate</a>';
            _this.html(html);
        });

        return false;
    });

    $(document).on("click", "a.confirm", function () {
        var _this = $(this).parent();
        var href = $(this).attr("href");
        var msg = $(this).data("msg");
        msg = msg ? msg : "Are You Sure?";

        confirmDialog(msg, function () {
            window.location.href = href;
        });

        return false;
    });

    $(document).on("submit", "form.confirm-submit", function () {
        var _this = $(this);
        var msg = _this.data("msg");
        var is_confirm = _this.data("is_confirm");

        if (!is_confirm) {
            msg = msg ? msg : "Are You Sure?";

            confirmDialog(msg, function () {
                _this.attr("data-is_confirm", 1);
                _this.trigger("submit");
            });

            return false;
        }
    });

    $(document).on("change", ".summary_table_with_search", function () {
        summary_table_with_search($(this));
    });

    $(document).on("keyup", ".summary_table_with_search input", function (e) {
        var _this = $(this);
        var name = $(this).attr("name");

        if (e.which == 27) {
            $(this).val("");
        }

        if (e.which == 13 || e.which == 27) {
            summary_table_with_search(_this.closest(".summary_table_with_search"), function () {
                $("input[name='" + name + "']").focus();
            });
        }
    });

    $(document).on("click", ".summary_table_with_search .clear_search", function () {
        var _this = $(this);
        _this.clearForm();
        summary_table_with_search(_this);
    });

    $(document).on("change", ".table-thead-search", function()
    {
        table_thead_form_ajax_submit($(this));
    });
});

function table_thead_form_ajax_submit(ele)
{
    $(ele).closest("form").ajaxSubmit({
        target: "#index_table",
        beforeSubmit: function (formData, jqForm, options) {
            $.loader.show();
        },
        success: function (responseText, statusText, xhr, $form) {
            $.loader.hide();
        },
        error: function () {
            $.loader.hide();
        },
    });

    return false;
}

function summary_table_with_search(ele, callback) {
    var base_url = $(ele).data("base_url");

    var form_data = {};

    $(ele).find("thead input, thead select, .search_field").each(function () {

        var val = $(this).val();

        var type = $(this).attr("type");

        if (type == "checkbox" || type == "radio") {
            val = $(this).prop("checked");
        }

        form_data[$(this).attr("name")] = val;
    });

    console.log(base_url, form_data);

    $(ele).ajaxSubmit({
        target: "#index_table",
        method: "GET",
        data: form_data,
        beforeSubmit: function (formData, jqForm, options) {
            $.loader.show();
        },
        success: function (responseText, statusText, xhr, $form) {
            $.loader.hide();
            if (typeof callback == "function") {
                callback();
            }
        },
        error: function () {
            $.loader.hide();
        },
    });
}

function suggestion_list(textarea, suggestionBox, suggestion_data, url) {
    textarea = $(textarea);
    if (textarea.length <= 0) {
        console.error("textarea not found");
        return;
    }

    suggestionBox = $(suggestionBox);

    if (suggestionBox.length <= 0) {
        console.error("suggestionBox not found");
        return;
    }

    if (url || suggestion_data) {

    }
    else {
        console.error("either data-url or data-suggestions is required");
        return;
    }

    let currentSearchTerm = '';
    let currentTriggerIndex = -1; // Position of the '#' character
    let activeSuggestionIndex = -1; // For keyboard navigation

    // Function to fetch suggestions (simulated or via AJAX)
    const fetchSuggestions = function (term) {
        if (url) {

            $.loader.indicator("Fetching Data...", textarea, {
                target : {
                    position : "top right",
                }
            });

            return $.get(url, { query: term }).fail(function (jqXHR, textStatus, errorThrown) {
                $.events.onAjaxError("Error fetching suggestions : " + textStatus, errorThrown);
                return []; // Return empty array on error
            });
        }

        $.loader.indicator("Filtering Data...", textarea, {
            target : {
                position : "top right",
            }
        });

        return new $.Deferred().resolve(
            suggestion_data.filter(s =>
                s.name.toLowerCase().includes(term.toLowerCase()) ||
                s.display.toLowerCase().includes(term.toLowerCase())
            )
        ).promise();
    };

    // Function to display suggestions
    const displaySuggestions = function (suggestions) {
        suggestionBox.empty().removeClass('hidden');
        activeSuggestionIndex = -1; // Reset active index

        if (suggestions.length === 0) {
            suggestionBox.addClass('hidden');
            return;
        }

        console.log(suggestions);

        $.each(suggestions, function (index, suggestion) {
            console.log(suggestion);
            const $item = $('<div>')
                .addClass('suggestion-item')
                .text(suggestion.display)
                .data('value', suggestion.name) // Store the #model.id:X part
                .data('index', index); // For keyboard navigation

            console.log($item.html());

            $item.on('click', function () {
                insertSuggestion($(this).data('value'));
            });
            suggestionBox.append($item);
        });
    };

    // Function to insert selected suggestion into textarea
    const insertSuggestion = function (value) {
        const text = textarea.val();
        const textBefore = text.substring(0, currentTriggerIndex);
        const textAfter = text.substring(currentTriggerIndex + currentSearchTerm.length + 1); // +1 for the #

        textarea.val(textBefore + value + ' ' + textAfter); // Add a space after insertion

        hideSuggestions();

        // Put cursor after the inserted text
        const newCursorPos = (textBefore + value + ' ').length;

        textarea[0].setSelectionRange(newCursorPos, newCursorPos);

        textarea.focus();
    };

    // Function to hide suggestions
    const hideSuggestions = function () {
        suggestionBox.addClass('hidden').empty();
        currentSearchTerm = '';
        currentTriggerIndex = -1;
        activeSuggestionIndex = -1;
        suggestionData = [];
    };

    // --- Event Handlers ---

    let inputTimeout = null; // For debouncing AJAX calls

    textarea.on('input', function (e) {
        const cursorPosition = this.selectionStart;
        const text = textarea.val();

        // Find the most recent '#' before the cursor that's not preceded by a space/newline
        let lastHashIndex = -1;
        for (let i = cursorPosition - 1; i >= 0; i--) {
            if (text[i] === '#') {
                lastHashIndex = i;
                break;
            }

            // if (/\s/.test(text[i])) 
            // { 
            //     // If space/newline found before '#', it's not a direct hash trigger
            //     lastHashIndex = -1;
            //     break;
            // }
        }

        if (lastHashIndex !== -1 && lastHashIndex < cursorPosition) {
            currentTriggerIndex = lastHashIndex;
            // Extract the term after '#' up to the cursor or a space/newline
            const partialTerm = text.substring(lastHashIndex + 1, cursorPosition);
            currentSearchTerm = partialTerm;
            console.log("search term", currentSearchTerm);

            if (currentSearchTerm.length > 0) {
                // Debounce the fetch request
                clearTimeout(inputTimeout);
                inputTimeout = setTimeout(function () {
                    currentSearchTerm = encodeURIComponent(currentSearchTerm);
                    fetchSuggestions(currentSearchTerm).done(
                        function (data) {

                            if (url)
                            {
                                data = ajaxHandleResponse(url, data);
                                suggestions = data['data'];
                            }

                            $.loader.indicator(false);

                            displaySuggestions(suggestions);
                        });
                }, 250); // Wait 250ms after last input
            } else {
                hideSuggestions();
            }
        } else {
            hideSuggestions();
        }
    });

    textarea.on('keydown', function (e) {
        if (suggestionBox.hasClass('hidden')) return;

        const $items = suggestionBox.children('.suggestion-item');
        if ($items.length === 0) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeSuggestionIndex = (activeSuggestionIndex + 1) % $items
                .length;
            updateActiveSuggestion($items);
            $items.eq(activeSuggestionIndex)[0].scrollIntoView({
                block: 'nearest'
            });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeSuggestionIndex = (activeSuggestionIndex - 1 + $items
                .length) % $items.length;
            updateActiveSuggestion($items);
            $items.eq(activeSuggestionIndex)[0].scrollIntoView({
                block: 'nearest'
            });
        } else if (e.key === 'Enter') {
            if (activeSuggestionIndex !== -1) {
                e.preventDefault(); // Prevent newline in textarea
                insertSuggestion($items.eq(activeSuggestionIndex).data(
                    'value'));
            }
        } else if (e.key === 'Escape') {
            hideSuggestions();
        }
    });

    // Handle clicks outside the suggestion box to hide it
    $(document).on('click', function (e) {
        if (!textarea.is(e.target) && !suggestionBox.has(e.target)
            .length) {
            hideSuggestions();
        }
    });

    function updateActiveSuggestion($items) {
        $items.removeClass('selected');
        if (activeSuggestionIndex !== -1) {
            $items.eq(activeSuggestionIndex).addClass('selected');
        }
    }
}