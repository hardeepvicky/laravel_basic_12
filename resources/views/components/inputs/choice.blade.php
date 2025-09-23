<?php
if (!isset($value)) {
    $value = '';
}

if (!isset($errorName)) {
    $errorName = $name;
}

if (!isset($mandatory)) {
    $mandatory = $attributes->get('required', false);
}


if (isset($isDisabled) && $isDisabled) {
    $is_disabled = true;
}

if (!isset($is_disabled))
{
    $is_disabled = $attributes->get('disabled', false);
}

$my_attributes = [];

if (isset($preventCssClassAddToInput) && $preventCssClassAddToInput) {
} else {
    $my_attributes['class'] = 'form-control choice invalid-char';
}

if ($mandatory) {
    $my_attributes['required'] = 'true';
}

if ($is_disabled) {
    $my_attributes['disabled'] = 'true';
}

$autocomplete = $attributes->get('autocomplete', "off");

if ($autocomplete) {
    $my_attributes['aria-autocomplete'] = $autocomplete;
}

?>

@if($layout == "horizontal")
     <div class="row mb-3">
        <div class="col-sm-{{ $horizontalFirstColumnLength }}">
            @if (isset($label) && $label)
                <label class="form-label">
                    {{ $label }}

                    @if ($mandatory)
                        <span class="mandatory">*</span>
                    @endif
                </label>
            @endif
        </div>
        <div class="col-sm-{{ $horizontalSecondColumnLength }}">
            <div class="form-group">
                <input name="{{ $name }}" value="{!! old($name, $value) !!}" {{ $attributes->merge($my_attributes) }} />
            </div>

            @error($errorName)
                <div class="input-error">{{ $message }}</div>
            @enderror
        </div>
    </div>
@else

    @if (isset($label) && $label)
    <label class="form-label">
        {{ $label }}

        @if ($mandatory)
        <span class="mandatory">*</span>
        @endif
    </label>
    @endif

    <input name="{{ $name }}" value="{{ old($name, $value) }}" {{ $attributes->merge($my_attributes) }} />

    @error($errorName)
    <div class="input-error">{{ $message }}</div>
    @enderror

@endif

<script>
    $(function() {

        $.events.execAtLast(function() {
            const my_choice = new Choices($('.choice')[0], {
                silent: false,
                items: [],
                choices: [],
                renderChoiceLimit: -1,
                maxItemCount: -1,
                closeDropdownOnSelect: 'auto',
                singleModeForMultiSelect: false,
                addChoices: false,
                addItems: true,
                addItemFilter: (value) => !!value && value !== '',
                removeItems: true,
                removeItemButton: true,
                removeItemButtonAlignLeft: false,
                editItems: false,
                allowHTML: false,
                allowHtmlUserInput: false,
                duplicateItemsAllowed: false,
                delimiter: ',',
                paste: true,
                searchEnabled: true,
                searchChoices: true,
                searchFloor: 1,
                searchResultLimit: 4,
                searchFields: ['label', 'value'],
                position: 'auto',
                resetScrollPosition: true,
                shouldSort: true,
                shouldSortItems: false,
                shadowRoot: null,
                placeholder: true,
                placeholderValue: null,
                searchPlaceholderValue: null,
                prependValue: null,
                appendValue: null,
                renderSelectedChoices: 'auto',
                loadingText: 'Loading...',
                noResultsText: 'No results found',
                noChoicesText: 'No choices to choose from',
                itemSelectText: 'Press to select',
                uniqueItemText: 'Only unique values can be added',
                customAddItemText: 'Only values matching specific conditions can be added',
                addItemText: (value) => {
                    return `Press Enter to add <b>"${value}"</b>`;
                },
                removeItemIconText: () => `Remove item`,
                removeItemLabelText: (value) => `Remove item: ${value}`,
                maxItemText: (maxItemCount) => {
                    return `Only ${maxItemCount} values can be added`;
                },
                valueComparer: (value1, value2) => {
                    return value1 === value2;
                },
                classNames: {
                    containerOuter: ['choices'],
                    containerInner: ['choices__inner'],
                    input: ['choices__input'],
                    inputCloned: ['choices__input--cloned'],
                    list: ['choices__list'],
                    listItems: ['choices__list--multiple'],
                    listSingle: ['choices__list--single'],
                    listDropdown: ['choices__list--dropdown'],
                    item: ['choices__item'],
                    itemSelectable: ['choices__item--selectable'],
                    itemDisabled: ['choices__item--disabled'],
                    itemChoice: ['choices__item--choice'],
                    description: ['choices__description'],
                    placeholder: ['choices__placeholder'],
                    group: ['choices__group'],
                    groupHeading: ['choices__heading'],
                    button: ['choices__button'],
                    activeState: ['is-active'],
                    focusState: ['is-focused'],
                    openState: ['is-open'],
                    disabledState: ['is-disabled'],
                    highlightedState: ['is-highlighted'],
                    selectedState: ['is-selected'],
                    flippedState: ['is-flipped'],
                    loadingState: ['is-loading'],
                    notice: ['choices__notice'],
                    addChoice: ['choices__item--selectable', 'add-choice'],
                    noResults: ['has-no-results'],
                    noChoices: ['has-no-choices'],
                },
                // Choices uses the great Fuse library for searching. You
                // can find more options here: https://fusejs.io/api/options.html
                fuseOptions: {
                    includeScore: true
                },
                labelId: '',
                callbackOnInit: null,
                callbackOnCreateTemplates: null,
                appendGroupInSearch: false,
            });
        });
    });
</script>