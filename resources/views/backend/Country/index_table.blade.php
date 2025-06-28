<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr class="border-bottom-primary">
                        <th><?= sortable_anchor('id', 'ID') ?></th>
                        <th><?= sortable_anchor('name', 'Name') ?></th>
                        <th><?= sortable_anchor('short_name', 'Short Name') ?></th>
                        <th><?= sortable_anchor('phonecode', 'Phone Code') ?></th>
                        <th>Timezone</th>
                        <th><?= sortable_anchor('iso3', 'Phone Name') ?></th>
                        <th><?= sortable_anchor('currency_name', 'Currency') ?></th>
                        <th>Info</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <th scope="row">{{ $record->id }}</th>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->short_name }}</td>
                            <td>{{ $record->phonecode }}</td>
                            <td>{{ $record->timezone }}</td>
                            <td>{{ $record->iso3 }}</td>
                            <td>{{ $record->currency_name }}</td>
                            <td>
                                <x-Backend.index-table-info :record="$record" :userList="$userListCache" />
                            </td>
                            <td>
                                <x-Backend.summary-comman-actions :id="$record->id" :routePrefix="$routePrefix" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <x-Backend.pagination-links :records="$records" />
    </div>
</div>
