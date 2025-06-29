<?php
use App\Helpers\DateUtility;

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
    <div class="label">By : {{ $userList[$record->created_by] ?? '' }}</div>
</div>

<div class="celec_info">
    <div class="label label-header">Updated</div>
    <div class="divider"></div>
    <div class="label">{{ $updated_array['date'] }}</div>
    <div class="divider"></div>
    <div class="label">{{ $updated_array['day'] }} {{ $updated_array['time'] }}</div>
    <div class="divider"></div>
    <div class="label">By : {{ $userList[$record->updated_by] ?? '' }}</div>
</div>
