<?php
$my_attributes = [];
if ($form_method == 'GET') {
    if (isset($preventCssClassAdd) && $preventCssClassAdd) {
    } else {
        $my_attributes['class'] = 'form-get-encode-before-submit';
    }
}
?>
<form action="{{ $action }}" method="{{ $form_method }}" {{ $attributes->merge($my_attributes) }}>
     @csrf
    {{ method_field($method) }}
