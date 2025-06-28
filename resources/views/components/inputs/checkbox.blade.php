<?php
if (!isset($value)) {
    $value = '';
}

if (!isset($errorName)) {
    $errorName = $name;
}

if (!isset($is_disabled)) {
    $is_disabled = (bool) $attributes->get('disabled', false);
}

$my_attributes = [];

if (isset($preventCssClassAddToInput) && $preventCssClassAddToInput) {
} else {
    $my_attributes['class'] = 'form-check-input primary';
}

if ($is_disabled) {
    $my_attributes['disabled'] = 'true';
}

if (old($name, $value)) {
    $my_attributes['checked'] = 'true';
}

$id = $attributes->get('id', $name);
?>

<label class="form-check">
    <input type="hidden" name="{{ $name }}" value="0" />
    <input type="checkbox" name="{{ $name }}" value="1" id="{{ $id }}"
        {{ $attributes->merge($my_attributes) }} />
    @if (isset($label) && $label)
        <label class="form-check-label" for="{{ $id }}">{{ $label }}</label>
    @endif
</label>

@error($errorName)
    <div class="input-error">{{ $message }}</div>
@enderror
