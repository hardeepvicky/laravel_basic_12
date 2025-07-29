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
    $my_attributes['class'] = 'form-control invalid-char';
}

if ($mandatory) {
    $my_attributes['required'] = 'true';
}

if ($is_disabled) {
    $my_attributes['disabled'] = 'true';
}

$autocomplete = $attributes->get('autocomplete', false);

if ($autocomplete) {
    $my_attributes['aria-autocomplete'] = $autocomplete;
}

?>

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
