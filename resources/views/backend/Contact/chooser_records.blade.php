<x-Backend.pagination-links :records="$records" />
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr class="border-bottom-primary">
                <th style="width:15%">
                    @if($multiple)
                    <label class="form-check custom-checkbox-group">
                        <input type="checkbox" class="form-check-input chk-select-all" data-sr-chkselect-children="input.contact">
                    </label>
                    @endif
                    <?= sortable_anchor('name', 'Name') ?>
                </th>
                <th style="width:8%"><?= sortable_anchor('type', 'Type') ?></th>
                <th style="width:15%"><?= sortable_anchor('mobile', 'Mobile') ?></th>
                <th style="width:15%"><?= sortable_anchor('mobile2', 'Mobile2') ?></th>
                <th style="width:15%"><?= sortable_anchor('email', 'Email') ?></th>
                <th style="width:10%"><?= sortable_anchor('state_name', 'State') ?></th>
                <th style="width:10%"><?= sortable_anchor('distirct_name', 'District') ?></th>
                <th style="width:10%"><?= sortable_anchor('city_name', 'City') ?></th>                
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $record)
                <tr>
                    <td>
                        <label class="form-check custom-checkbox-group">
                            <input type="checkbox" class="form-check-input contact" data-type="{{ $record->type }}" value={{ $record->id }}>
                            <span>{{ $record->name }}</span>
                        </label>
                    </td>
                    <td>{{ get_value_from_list($type_list, $record->type) }}</td>
                    <td>{{ $record->mobile }}</td>
                    <td>{{ $record->mobile2 }}</td>
                    <td>{{ $record->email }}</td>
                    <td>{{ $record->state_name }}</td>
                    <td>{{ $record->distirct_name }}</td>
                    <td>{{ $record->city_name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<x-Backend.pagination-links :records="$records" />