<?php

use App\Helpers\DateUtility;
use App\Helpers\LaravelExtend;
use Illuminate\Support\Carbon;

if ($option_array['show_as'] == "celec_datetime" || $option_array['show_as'] == "celec_date"):

    $date_array = DateUtility::getCelecDate($datetime);
?>
    <div class="celec_info">
        <div class="label">{{ $date_array['date'] }}</div>
        <div class="divider"></div>
        <div class="label">{{ $date_array['day'] }} {{ $date_array['time'] }}</div>
        <div class="divider"></div>
        <div class="label">By : {{ LaravelExtend::getCreatedByOrUpdatedBy($userId) }}</div>
    </div>
<?php elseif ($option_array['show_as'] == "human_date" || $option_array['show_as'] == "human_datetime"): 
?>
    <div class="celec_info">
        <div class="label">{{ $datetime->diffForHumans() }}</div>
        <div class="divider"></div>
        <div class="label">By : {{ LaravelExtend::getCreatedByOrUpdatedBy($userId) }}</div>
    </div>

    <div class="celec_info">
        <div class="label">{{ $datetime->diffForHumans() }}</div>
        <div class="divider"></div>
        <div class="label">By : {{ LaravelExtend::getCreatedByOrUpdatedBy($userId) }}</div>
    </div>
<?php else: ?>
    <?php
    $format = "d-M-Y H:i:s";

    if ($option_array['show_as'] == "date") {
        $format = DateUtility::DATE_OUT_FORMAT;
    } else if ($option_array['show_as'] == "datetime") {
        $format = DateUtility::DATETIME_OUT_FORMAT;
    } else if ($option_array['show_as'] == "full_datetime") {
        $format = "D, d-F-Y H:i:s A";
    }
    ?>
    <div class="celec_info">
        <div class="label">{{ $datetime->format($format) }}</div>
        <div class="divider"></div>
        <div class="label">By : {{ LaravelExtend::getCreatedByOrUpdatedBy($userId) }}</div>
    </div>

    <div class="celec_info">
        <div class="label">{{ $datetime->format($format) }}</div>
        <div class="divider"></div>
        <div class="label">By : {{ LaravelExtend::getCreatedByOrUpdatedBy($userId) }}</div>
    </div>
<?php endif; ?>