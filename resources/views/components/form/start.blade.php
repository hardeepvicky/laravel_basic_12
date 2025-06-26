<form action="{{ $action }}" method="{{ $form_method }}"  {{ $attributes->merge(['class' => '']) }}>
    {!! csrf_field() !!}
    {{ method_field($method) }}