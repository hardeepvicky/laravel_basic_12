<?php
use App\Helpers\DateUtility;
use App\Helpers\LaravelExtend;

if ($options['show_as'] == "celec_datetime"):

$created_array = DateUtility::getCelecDate($record->created_at);
$updated_array = DateUtility::getCelecDate($record->updated_at);
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
<?php elseif ($options['show_as'] == "human_date" || $options['show_as'] == "human_date"): ?>
<div class="celec_info">
    <div class="label label-header">Created</div>
    <div class="divider"></div>
    <div class="label">{{ $record->created_at->diffForHumans() }}</div>
    <div class="divider"></div>
    <div class="label">By : {{ LaravelExtend::getCreatedByOrUpdatedBy($record->created_by) }}</div>
</div>

<div class="celec_info">
    <div class="label label-header">Updated</div>
    <div class="divider"></div>
    <div class="label">{{ $record->updated_at->diffForHumans() }}</div>
    <div class="divider"></div>
    <div class="label">By : {{ LaravelExtend::getCreatedByOrUpdatedBy($record->updated_by) }}</div>
</div>
<?php else: ?>

<?php 
$format = "d-M-Y H:i:s";

if ($options['show_as'] == "date")
{
    $format = DateUtility::DATE_OUT_FORMAT;
}
else if ($options['show_as'] == "datetime")
{
    $format = DateUtility::DATETIME_OUT_FORMAT;
}   
else if ($options['show_as'] == "full_datetime")
{
    $format = "d-F-Y H:i:s.u A, D";
}
?>
<div class="celec_info">
    <div class="label label-header">Created</div>
    <div class="divider"></div>
    <div class="label">{{ $record->created_at->format($format) }}</div>
    <div class="divider"></div>
    <div class="label">By : {{ LaravelExtend::getCreatedByOrUpdatedBy($record->created_by) }}</div>
</div>

<div class="celec_info">
    <div class="label label-header">Updated</div>
    <div class="divider"></div>
    <div class="label">{{ $record->updated_at->format($format) }}</div>
    <div class="divider"></div>
    <div class="label">By : {{ LaravelExtend::getCreatedByOrUpdatedBy($record->updated_by) }}</div>
</div>
<?php endif; ?>
