<?php
$my_attributes = [];

if (isset($preventDefaultCssClass) && $preventDefaultCssClass) {
} else {
    $my_attributes['class'] = '';
}
?>

@isset($headerText)
    <h6 class="sub-title f-w-600">{!! $headerText !!}</h6>
@endisset

@if ($type == 'ol')
    <ol {{ $attributes->merge($my_attributes) }}>
    @else
        <ul {{ $attributes->merge($my_attributes) }}>
@endif

@foreach ($list as $k => $v)
    <li data-key="{{ $k }}">
        @if (is_array($v))
            <x-common-list :list="$v" :headerText="$k" />
        @elseif(is_scalar($v))
            {!! $v !!}
        @endif
    </li>
@endforeach

@if ($type == 'ol')
    </ol>
@else
    </ul>
@endif
