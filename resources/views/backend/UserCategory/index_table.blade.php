<div class="card" id="page-summary">
    <div class="card-header">
        <x-Backend.pagination-links :records="$records" />
    </div>

    <div class="card-body p-1 summary_table_with_search" data-base_url="{{ route($routePrefix . '.index') }}">

        <div class="row mb-2">
            <div class="col-md-12 col-lg-6">
                Limit :
                <?php $false = false; ?>
                <x-Inputs.drop-down name="pagination_limit" label="" value="{{ $pagination_limit }}"
                    :list="$paginationList" class="form-control-sm search_field inline" :pleaseSelect="$false" />

                <span class="btn btn-secondary btn-sm clear_search">Clear</span>
            </div>
            <div class="col-md-12 col-lg-6">

            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered summary-table">
                <thead>
                    <tr class="border-bottom-primary">
                        <th style="width: 10%;">
                            <x-Inputs.text-field name="id" label="" :value="$search['id']" class="form-control-sm" />
                            <hr />
                            <?= sortable_anchor('id', 'ID') ?>
                        </th>
                        <th style="width: 10%;">
                            <x-Inputs.drop-down name="type" label="" :value="$search['type']" :list="$type_list"
                                pleaseSelect="All" class="form-control-sm" />
                            <hr />
                            <?= sortable_anchor('type', 'Type') ?>
                        </th>
                        <th style="width: 10%;">
                            <x-Inputs.text-field name="name" label="" :value="$search['name']"
                                class="form-control-sm" />
                            <hr />
                            <?= sortable_anchor('name', 'Name') ?>
                        </th>
                        <th style="width: 10%;">
                            <x-Inputs.text-field name="description" label="" :value="$search['description']"
                                class="form-control-sm" />
                            <hr />
                            <?= sortable_anchor('description', 'Description') ?>
                        </th>
                        <th style="width: 10%;">
                            <x-Inputs.drop-down name="is_active" label="" :value="$search['is_active']"
                                :list="$yes_no_list" pleaseSelect="All" class="form-control-sm" />
                            <hr />
                            <?= sortable_anchor('is_active', 'Active') ?>
                        </th>
                        <th style="width: 10%;">
                            <?= sortable_anchor('is_pre_defined', 'Pre-Defined') ?>
                        </th>
                        <th>Info</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                    <tr>
                        <th scope="row">{{ $record->id }}</th>
                        <td>{{ get_value_from_list($type_list, $record->type) }}</td>
                        <td>{{ $record->name }}</td>
                        <td>{{ $record->description }}</td>
                        <td>
                            <x-Backend.active_deactive :isActive="$record->is_active" :id="$record->id"
                                :routePrefix="$routePrefix" />
                        </td>
                        <td>
                            <x-Backend.yes-no-label :value="$record->is_pre_defined" />
                        </td>
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