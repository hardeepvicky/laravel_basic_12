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
    

    <div class="p-1">
        <x-Backend.summary-comman-actions :id="$record->id" :routePrefix="$routePrefix" :buttons="$buttons"/>
        <span class="btn btn-info btn-xs css-toggler mt-1 mb-1"
            data-sr-css-class-toggle-target="#record-{{ $record->id }}"
            data-sr-css-class-toggle-class="hidden" data-sr-css-class-toggle-applied-text="Show Detail"
            data-sr-css-class-toggle-not-applied-text="Hide">
            Show Detail
        </span>
    </div>
</div>