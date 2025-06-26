<?php
if (!isset($mandatory)) {
    $mandatory = false;
}

if (!isset($is_disabled)) {
    $is_disabled = false;
}

if (!isset($value)) {
    $value = '';
}

if (!isset($errorName) || empty($errorName)) {
    $errorName = $name;
}

if ($is_disabled) {
    $mandatory = false;
}

$my_attributes = [
    'type' => 'text',
];

if (isset($preventCssClassAddToInput) && $preventCssClassAddToInput) {
} else {
    $my_attributes['class'] = 'form-control invalid-char';
}

if ($mandatory) {
    $my_attributes['required'] = 'true';
}

if ($is_disabled) {
    $my_attributes['disabled'] = true;
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
