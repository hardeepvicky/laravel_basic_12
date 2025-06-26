<?php 
if (!isset($value)){
    $value = "";
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

@if(isset($label) && $label)
<label class="form-label">
    {{ $label }}
    @if($mandatory)
        <span class="mandatory">*</span>
    @endif
</label>
@endif

<?php
   $value_list = [];

   $name_without_square_braces = str_replace("[]", "", $name);

   $v = old($name_without_square_braces, $value);

   if (is_string($v) && strpos($v, ",") !== false)
   {
        $value_list = $v ? explode(",", $v) : [];
   }
   else if (is_array($v))
   {
        $value_list = $v;
   }
   else
   {
        $value_list[] = $v;
   }

?>

<select name="{{ $name }}" {{ $attributes->merge($my_attributes) }}>
    @if (!$attributes->has('multiple')) 
        @if($pleaseSelect)
            <option value="">Please Select</option>
        @endif
    @endif
    @foreach($list as $k => $t)
        @php 
            $attr = in_array($k, $value_list) ? 'selected="selected"' : "";
        @endphp
        <option value="{{ $k }}" {!! $attr !!} >{{$t}}</option>
    @endforeach
</select>

@if($is_disabled)
   <input type="hidden" name="{{ $name }}" value="{{ implode(',', $value_list) }}" />
@endif

@error($errorName)
    <div class="input-error">{{ $message }}</div>
@enderror
