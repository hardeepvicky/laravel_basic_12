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

$div_class = 'form-check';

if ($attributes->get('role') == 'switch') {
    $div_class .= ' form-switch';
} else {
    $div_class .= ' custom-checkbox-group';
}

$my_attributes = [];

if (isset($preventCssClassAddToInput) && $preventCssClassAddToInput) {
} else {
    $my_attributes['class'] = 'form-check-input';
}

if ($is_disabled) {
    $my_attributes['disabled'] = 'disabled';
}

$v = old($name, $value);
if ($v) {
    $my_attributes['checked'] = 'checked';
}


$id = $attributes->get('id', $name);
?>

<label class="{{ $div_class }}">
    @if (isset($hiddenField) && $hiddenField)
        <input type="hidden" name="{{ $name }}" value="0" />
    @endif

    <input type="checkbox" name="{{ $name }}" value="1" id="{{ $id }}" {{ $attributes->except(["value", "id", "type", "name"])->merge($my_attributes) }} />
    @if (isset($label) && $label)
        <span class="form-check-label" for="{{ $id }}">{{ $label }}</span>
    @endif
</label>

@error($errorName)
<div class="input-error">{{ $message }}</div>
@enderror