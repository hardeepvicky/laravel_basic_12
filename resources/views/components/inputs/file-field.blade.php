<?php 
<<<<<<< HEAD
if (!isset($mandatory)){
    $mandatory = false;
}

?>

=======
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
];

if (isset($preventCssClassAddToInput) && $preventCssClassAddToInput) {
} else {
    $my_attributes['class'] = 'form-control';
}

if ($mandatory) {
    $my_attributes['required'] = 'true';
}

if (!$mandatory && $is_disabled) {
    $my_attributes['disabled'] = "true";
}
?>

@if (isset($label) && $label)
>>>>>>> base_v3
<label class="form-label">
    {{ $label }}

    @if($mandatory)
        <span class="mandatory">*</span>
    @endif
</label>
<<<<<<< HEAD
<input type="file"  name="{{ $name }}" <?= $mandatory ? "required" : "" ?> {{ $attributes->merge(['class' => 'form-control',  ]) }}   />
@error($name)
    <div class="input-error">{{ $message }}</div>
@enderror
=======
@endif

<input type="file"  name="{{ $name }}" {{ $attributes->merge($my_attributes) }}   />

@error($errorName)
    <div class="input-error">{{ $message }}</div>
@enderror

>>>>>>> base_v3
