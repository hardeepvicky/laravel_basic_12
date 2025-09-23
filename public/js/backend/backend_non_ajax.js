/**
 * @author : Hardeep
 * this file only for backend
 */

$(document).ready(function () {
<<<<<<< HEAD
    $.loader.init();

    $("form").find("div.pristine-error").parents(".form-group").addClass("has-danger");
=======
    $("form")
        .find("div.pristine-error")
        .parents(".form-group")
        .addClass("has-danger");
>>>>>>> base_v3

    $("input[type='checkbox'].chk-select-all").chkSelectAll();

    $(".css-toggler").cssClassToggle();

    $(".ajax-load").ajaxLoad();

    $(".table-export-csv").srTableCSVExport();

<<<<<<< HEAD
    $(".select2").select2({
        placeHolder: "Please Select",
        theme: "bootstrap-5",
=======
    $("select.select2").not("select2-hidden-accessible").select2({
        placeHolder : "Please Select",
        theme : "bootstrap-5",
>>>>>>> base_v3
    });

    $(".fancybox").fancybox();

    $(".date-picker").flatpickr({
<<<<<<< HEAD
        dateFormat: "d-M-Y",
    });

    $(".date-time-picker").flatpickr({
        enableTime: true,
        time_24hr: false,
        dateFormat: "d-M-Y h:i K",
    });


    $('.time-picker').flatpickr({
        noCalendar: true,
        enableTime: true,
        dateFormat: 'h:i K'
=======
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
        time_24hr: true
>>>>>>> base_v3
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

<<<<<<< HEAD


    /** -------------------------------------------------------- */



    $('input.invalid-char').on('keypress', function (event) {
        var regex = new RegExp("^[a-zA-Z0-9~!@#$%^*()<>{}_.,/\+\-\\\\]+$");
        var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
=======
    $(".cascade-drop-down").cascade({
        beforeGet: function (src, url) {
            $.loader.init().setInfo("Fetching...").show();
            return url;
        },
        afterGet: function (src, dest, response) {
            $.loader.init().hide();
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
>>>>>>> base_v3
        key = key.trim();

        var error_span = $(this).parent().find(".error-message");
        if (error_span.length == 0) {
<<<<<<< HEAD
            $(this).parent().append('<span class="error-message">&#9679 Invalid Character</span>');
=======
            $(this)
                .parent()
                .append(
                    '<span class="error-message">&#9679 Invalid Character</span>'
                );
>>>>>>> base_v3
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
<<<<<<< HEAD
                confirmButtonText: "Yes, delete it!"
=======
                confirmButtonText: "Yes, delete it!",
>>>>>>> base_v3
            }).then(function (e) {
                if (e.value) {
                    _form.attr("data-confirm", 1);
                    _form.trigger("submit");
                }
            });

            return false;
        }
    });

<<<<<<< HEAD

    $(document).on("submit", "form.summary_search", function () {
        $(this).ajaxSubmit({
            target: '#index_table',
=======
    $(document).on("submit", "form.summary_search", function () {

        var load_html_at = $(this).data("load_html_at");

        if (load_html_at)
        {
            var target = $(load_html_at);

            if(target.length > 0)
            {
                if(target.length > 1)
                {
                    console.error(`${load_html_at} more than 1 found by jquery`);
                    return;
                }

                $.loader.init(target);
            }
            else
            {
                $.loader.init();
                target = $("#index_table");
            }
        }
        else
        {
            load_html_at = "#index_table";
            var target = $(load_html_at);
            $.loader.init();
        }

        $(this).ajaxSubmit({
>>>>>>> base_v3
            beforeSubmit: function (formData, jqForm, options) {
                $.loader.show();
            },
            success: function (responseText, statusText, xhr, $form) {
<<<<<<< HEAD
                $.loader.hide();
            },
            error: function () {
                $.loader.hide();
            }
=======
                console.log(target);
                target.html(responseText);
                $.loader.hide();
                $.loader.init().hide();
            },
            error: function () {
                $.loader.hide();
                $.loader.init().hide();
            },
>>>>>>> base_v3
        });

        return false;
    });

    $(document).on("click", "form .clear_form_search_conditions", function () {
        var _form = $(this).closest("form");

        _form.clearForm();

        _form.find("input[name='is_sort_clear']").val(1);

<<<<<<< HEAD
        var v = _form.find(".pagination_limit:first").find("option:first").val();
=======
        var v = _form
            .find(".pagination_limit:first")
            .find("option:first")
            .val();
>>>>>>> base_v3
        _form.find(".pagination_limit").val(v);

        _form.trigger("submit");
    });

    $(document).on("change", "form.summary_search select.pagination_limit", function () {
<<<<<<< HEAD
        var _form = $(this).closest("form");
        _form.trigger("submit");
    });

    $("#index_table").on("click", ".pagination a.page-link, a.sortable", function () {

        var href = $(this).attr("href");
        $.loader.show();

        $("#index_table").load(href, function () {
            $.loader.hide();
        });

        return false;
    });

    $(document).on("click", "a.activate", function () {

=======
            var _form = $(this).closest("form");
            _form.trigger("submit");
        }
    );

    $(document).on("click", ".pagination a.page-link, a.sortable", function(){
            var href = $(this).attr("href");

            var target = $(this).closest(".index_table");

            if(target.length > 0)
            {
                if(target.length > 1)
                {
                    console.error(`.index_table more than 1 found by jquery`);
                    return;
                }
                
                $.loader.init(target);
            }
            else
            {
                $.loader.init();
                target = $("#index_table");
            }

            if(target.length == 0)
            {
                console.error(`either .index_table or #index_table not found by jquery`);
                return;
            }

            $.loader.show();

            $(target).load(href, function () {
                $.loader.hide();
            });

            return false;
        }
    );

    $(document).on("click", "a.activate", function () {
>>>>>>> base_v3
        var _this = $(this).parent();
        var href = $(this).attr("href");

        ajaxGetJson(href, function (response) {
            var html = '<span class="badge bg-success">Active</span>';
<<<<<<< HEAD
            html += '<br/>'
            html += '<a class="de_activate" href="' + response['url'] + '">De-Activate</a>';
=======
            html += "<br/>";
            html +=
                '<a class="de_activate" href="' +
                response["url"] +
                '">De-Activate</a>';
>>>>>>> base_v3
            _this.html(html);
        });

        return false;
    });

    $(document).on("click", "a.de_activate", function () {
<<<<<<< HEAD

=======
>>>>>>> base_v3
        var _this = $(this).parent();
        var href = $(this).attr("href");

        ajaxGetJson(href, function (response) {
            var html = '<span class="badge bg-danger">De-Active</span>';
<<<<<<< HEAD
            html += '<br/>'
            html += '<a class="activate" href="' + response['url'] + '">Activate</a>';
=======
            html += "<br/>";
            html +=
                '<a class="activate" href="' +
                response["url"] +
                '">Activate</a>';
>>>>>>> base_v3
            _this.html(html);
        });

        return false;
    });

    $(document).on("click", "a.confirm", function () {
<<<<<<< HEAD

=======
>>>>>>> base_v3
        var _this = $(this).parent();
        var href = $(this).attr("href");
        var msg = $(this).data("msg");
        msg = msg ? msg : "Are You Sure?";

        confirmDialog(msg, function () {
            window.location.href = href;
<<<<<<< HEAD
        })

        return false;
    });
});


=======
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

    $(document).on("change", ".summary_table_with_search select, .summary_table_with_search input", function () {
        summary_table_with_search($(this));
    });

    $(document).on("keyup", ".summary_table_with_search input", function (e) {
        var _this = $(this);
        var name = $(this).attr("name");

        if (e.which == 27) {
            $(this).val("");
        }

        if (e.which == 13 || e.which == 27) {
            summary_table_with_search(_this, function () {
                $("input[name='" + name + "']").focus();
            });
        }
    });

    $(document).on("input", ".summary_table_with_search input", function (e) {
        var _this = $(this);
        var name = $(this).attr("name");

        if (e.which == 13 || e.which == 27) {
            summary_table_with_search(_this, function () {
                $("input[name='" + name + "']").focus();
            });
        }
    });

    $(document).on("click", ".summary_table_with_search .clear_search_conditions", function (e) {

        $("form.summary_search").find("input[type='text']").val("");
        $("form.summary_search").find("select").val("").trigger("change");
        $("form.summary_search").find("input[type='checkbox'], input[type='radio']").prop("checked", false);
        
        $("form.summary_search").trigger("submit");
    });

    $(document).on("click", "a.ajaxify", function(e){
        e.preventDefault();

        var callback = $(this).data("callback");
        var process_info = $(this).data("process_info");

        if (!process_info)
        {
            process_info = "Sending Request....";
        }

        $.loader.init().setInfo(process_info).show();

        $.get($(this).attr("href"), function(response){

            $.loader.hide();

            if (typeof callback == "string")
            {
                eval(`${callback}();`);
            }
            else if (typeof callback == "function")
            {
                callback();
            }
        })

        return false;
    })
});
    

function summary_table_with_search(ele, callback) {
    var name = $(ele).attr("name");

    var search_input = $("form.summary_search [name='" + name + "']");

    if (search_input.length == 0)
    {
        console.error(`summary_table_with_search -> element with name : ${name} not found in form.summary_search`);
        return;
    }
    else if (search_input.length > 1)
    {
        console.error(`summary_table_with_search -> element with name : ${name} more than 1 found in form.summary_search`);
        return;
    }

    search_input.val($(ele).val()).trigger("change");
    
    $("form.summary_search").trigger("submit");
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

            $.loader.indicator("Fetching Data...", textarea.parent(), {
                target : {
                    position : "top right",
                }
            });

            return $.get(url, { query: term }).fail(function (jqXHR, textStatus, errorThrown) {
                $.events.onAjaxError("Error fetching suggestions : " + textStatus, errorThrown);
                return []; // Return empty array on error
            });
        }
        else
        {
            $.loader.indicator("Filtering Data...", textarea.parent(), {
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
        }
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
                    if (url)
                    {
                        fetchSuggestions(currentSearchTerm).done(function (data) {
                                data = ajaxHandleResponse(url, data);
                                suggestions = data['data'];

                            $.loader.indicator(false, textarea.parent());

                            displaySuggestions(suggestions);
                        });
                    }
                    else
                    {
                        fetchSuggestions(currentSearchTerm).then(function (suggestions) {
                               
                            $.loader.indicator(false, textarea.parent());

                            displaySuggestions(suggestions);
                        });
                    }
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

function placeholder_suggestion(textarea, suggestionBox, suggestion_data, url)
{
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

            $.loader.indicator("Fetching Data...", textarea.parent(), {
                target : {
                    position : "top right",
                }
            });

            return $.get(url, { query: term }).fail(function (jqXHR, textStatus, errorThrown) {
                $.events.onAjaxError("Error fetching suggestions : " + textStatus, errorThrown);
                return []; // Return empty array on error
            });
        }
        else
        {
            $.loader.indicator("Filtering Data...", textarea.parent(), {
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
        }
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
            if (text[i] === '@') {
                lastHashIndex = i;
                break;
            }

            if (/\s/.test(text[i])) 
            { 
                // If space/newline found before '#', it's not a direct hash trigger
                lastHashIndex = -1;
                break;
            }
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
                    if (url)
                    {
                        fetchSuggestions(currentSearchTerm).done(function (data) {
                                data = ajaxHandleResponse(url, data);
                                suggestions = data['data'];

                            $.loader.indicator(false, textarea.parent());

                            displaySuggestions(suggestions);
                        });
                    }
                    else
                    {
                        fetchSuggestions(currentSearchTerm).then(function (suggestions) {
                               
                            $.loader.indicator(false, textarea.parent());

                            displaySuggestions(suggestions);
                        });
                    }
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
            activeSuggestionIndex = (activeSuggestionIndex + 1) % $items.length;
            updateActiveSuggestion($items);
            $items.eq(activeSuggestionIndex)[0].scrollIntoView({
                block: 'nearest'
            });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeSuggestionIndex = (activeSuggestionIndex - 1 + $items.length) % $items.length;
            updateActiveSuggestion($items);
            $items.eq(activeSuggestionIndex)[0].scrollIntoView({
                block: 'nearest'
            });
        } else if (e.key === 'Enter') {
            if (activeSuggestionIndex !== -1) {
                e.preventDefault(); // Prevent newline in textarea
                insertSuggestion($items.eq(activeSuggestionIndex).data('value'));
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
>>>>>>> base_v3
