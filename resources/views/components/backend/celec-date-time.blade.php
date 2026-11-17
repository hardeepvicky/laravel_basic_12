<?php
use App\Helpers\DateUtility;
if (isset($value) && $value):
$created_array = DateUtility::getCelecDate($value);
if ($created_array):
?>
<div class="celec_info">
    <?php if (isset($label) && $label): ?>
        <div class="label label-header">{{ $label }}</div>
        <div class="divider"></div>
    <?php endif; ?>
    <div class="label">{{ $created_array['date'] }}</div>
    <div class="divider"></div>
    <div class="label text-end">{{ $created_array['day'] }} {{ $created_array['time'] }}</div>
    <?php if (isset($by) && $by): ?>
        <div class="divider"></div>
        <div class="label text-end">{{ $by }}</div>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php endif; ?>