<?php
use App\Helpers\LaravelExtend;
?>

<x-Backend.pagination-links :records="$records" />

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr class="border-bottom-primary">
                <th style="width:5%">
                    @if($multiple)
                    <label class="form-check custom-checkbox-group">
                        <input type="checkbox" class="form-check-input chk-select-all" data-sr-chkselect-children="input.product">
                    </label>
                    @endif
                </th>
                <th style="width:15%"><?= sortable_anchor('model_name', 'Model Name') ?></th>
                <th style="width:15%">Image</th>
                <th style="width:10%">Created By</th>
                <th style="width:10%"><?= sortable_anchor('created_at', 'Created') ?></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $record)
                <tr>
                    <td>
                        <label class="form-check custom-checkbox-group">
                            <input type="checkbox" class="form-check-input product" value={{ $record->id }}>
                        </label>
                    </td>
                    <td>{{ $record->model_name }}</td>
                    <td>
                        @if (isset($record->mainImage) && $record->mainImage)
                            <img class="small-img lazy-load-image" style="max-width: 150px;" src="{{ $record->mainImage->file_url }}" alt="{{ $record->mainImage->name }}"/>
                        @endif
                    </td>
                    <td>{{ LaravelExtend::getCreatedByOrUpdatedBy($record->created_by) }}</td>
                    <td>{{ $record->created_at_user_tz->diffForHumans() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<x-Backend.pagination-links :records="$records" />