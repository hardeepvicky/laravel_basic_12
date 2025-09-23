<<<<<<< HEAD
<span class="badge bg-info">Created : {{ if_date_time($record->created_at)}}</span>
<span class="badge bg-info">By : {{ $userList[$record->created_by] ?? ""}}</span>
<br/>

<span class="badge bg-info">Updated : {{ if_date_time($record->updated_at)}}</span>
<span class="badge bg-info">By : {{ $userList[$record->updated_by] ?? ""}}</span>
=======
<?php

use App\Helpers\DateUtility;
use App\Helpers\LaravelExtend;
use Illuminate\Support\Carbon;

$created_at = $record->created_at_user_tz ?? $record->created_at;
$updated_at = $record->updated_at_user_tz ?? $record->updated_at;

if ($option_array['show_as'] == "celec_datetime" || $option_array['show_as'] == "celec_date"):

    $created_array = DateUtility::getCelecDate($created_at);
    $updated_array = DateUtility::getCelecDate($updated_at);
?>
    <div class="celec_info">
        <div class="label label-header">Created</div>
        <div class="divider"></div>
        <div class="label">{{ $created_array['date'] }}</div>
        <div class="divider"></div>
        <div class="label">{{ $created_array['day'] }} {{ $created_array['time'] }}</div>
        <div class="divider"></div>
        <div class="label">By : {{ LaravelExtend::getCreatedByOrUpdatedBy($record->created_by) }}</div>
    </div>

    <div class="celec_info">
        <div class="label label-header">Updated</div>
        <div class="divider"></div>
        <div class="label">{{ $updated_array['date'] }}</div>
        <div class="divider"></div>
        <div class="label">{{ $updated_array['day'] }} {{ $updated_array['time'] }}</div>
        <div class="divider"></div>
        <div class="label">By : {{ LaravelExtend::getCreatedByOrUpdatedBy($record->updated_by) }}</div>
    </div>
<?php elseif ($option_array['show_as'] == "human_date" || $option_array['show_as'] == "human_datetime"): 
?>
    <div class="celec_info">
        <div class="label label-header">Created</div>
        <div class="divider"></div>
        <div class="label">{{ $created_at->diffForHumans() }}</div>
        <div class="divider"></div>
        <div class="label">By : {{ LaravelExtend::getCreatedByOrUpdatedBy($record->created_by) }}</div>
    </div>

    <div class="celec_info">
        <div class="label label-header">Updated</div>
        <div class="divider"></div>
        <div class="label">{{ $updated_at->diffForHumans() }}</div>
        <div class="divider"></div>
        <div class="label">By : {{ LaravelExtend::getCreatedByOrUpdatedBy($record->updated_by) }}</div>
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
        <div class="label label-header">Created</div>
        <div class="divider"></div>
        <div class="label">{{ $created_at->format($format) }}</div>
        <div class="divider"></div>
        <div class="label">By : {{ LaravelExtend::getCreatedByOrUpdatedBy($record->created_by) }}</div>
    </div>

    <div class="celec_info">
        <div class="label label-header">Updated</div>
        <div class="divider"></div>
        <div class="label">{{ $updated_at->format($format) }}</div>
        <div class="divider"></div>
        <div class="label">By : {{ LaravelExtend::getCreatedByOrUpdatedBy($record->updated_by) }}</div>
    </div>
<?php endif; ?>
>>>>>>> base_v3
