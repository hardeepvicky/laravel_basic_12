<?php
use App\Helpers\LaravelExtend;
?>
<div class="celec_info">
    @if (isset($labelInfo) && $labelInfo)
        @foreach($labelInfo as $label)
            <div class="label text-center">{!! $label !!}</div>
            <div class="divider"></div>
        @endforeach
    @endif

    {!! $slot !!}

    <div class="divider"></div>

    <div class="p-1">
        <x-Backend.summary-comman-actions :id="$record->id" :routePrefix="$routePrefix" :buttons="$buttons"/>        
    </div>
</div>