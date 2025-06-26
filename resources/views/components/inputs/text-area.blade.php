<?php 
if (!isset($value)){
    $value = "";
} 

if (!isset($errorName) || empty($errorName)){
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
    'rows' => 4
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

<label class="form-label">
    {{ $label }}

    @if($mandatory)
        <span class="mandatory">*</span>
    @endif
</label>

<textarea name="{{ $name }}" {{ $attributes->merge($my_attributes) }}>{{ old($name, $value) }}</textarea>

@error($errorName)
    <div class="input-error">{{ $message }}</div>
@enderror
