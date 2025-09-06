<div class="card" id="page-summary">
    <div class="card-header border-t-info">
        <x-backend.pagination-links :records="$records" />
    </div>

    <div class="card-body">
        <table class="table table-striped table-bordered table-hover mb-0">
            <thead>
                <tr>
                    <th><?= sortable_anchor('role_id', 'Role') ?></th>
                    <th><?= sortable_anchor('section_name', 'Section') ?></th>
                    <th style="width:10%">
                        <?= sortable_anchor('type', 'Action Type') ?>
                    </th>
                    <th style="width:15%">
                        <?= sortable_anchor('created_at', 'Created') ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $record)
                    <tr>
                        <td>{{ get_value_from_list($role_list,$record->role_id) }}</td>
                        <td>{{ $record->section_name }}</td>
                        <td>{{ $record->type }}</td>
                        <td>
                            <x-Backend.created-and-by :datetime="$record->created_at_user_tz" :userId="$record->created_by" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <x-backend.pagination-links :records="$records" />
    </div>
</div>
