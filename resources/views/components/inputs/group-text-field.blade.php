<?php 
if (!isset($value)){
    $value = "";
} 

if (!isset($secondaryTextPosition))
{
    $secondaryTextPosition = "";
}

if (!isset($secondaryText))
{
    $secondaryText = "";
}

if (!isset($errorName)){
    $errorName = $name;
}

if (!isset($mandatory))
{
    $mandatory = $attributes->get("required", false);
}

if (!isset($is_disabled))
{
    $is_disabled = $attributes->get("disabled", false);
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

if (!$mandatory && $is_disabled) {
    $my_attributes['disabled'] = "true";
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

<div class="input-group">
    @if ($secondaryTextPosition == 'first')
        {!! $secondaryText !!}
    @endif
    <input name="{{ $name }}" value="{!! old($name, $value) !!}" {{ $attributes->merge($my_attributes) }} />

    @if ($secondaryTextPosition == 'last')
        {!! $secondaryText !!}
    @endif
</div>

@error($errorName)
    <div class="input-error">{{ $message }}</div>
@enderror
