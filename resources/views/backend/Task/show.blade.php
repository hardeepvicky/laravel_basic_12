<div class="card">
    <div class="card-header card-no-border pb-0">
        {{ $record->name }}
        <div class="card-header-right">
            <ul class="list-unstyled card-option" style="width: 230px;">
                <li><i class="fa-solid fa-maximize full-card"></i></li>
                <li><i class="fa-solid fa-minus minimize-card"></i></li>
            </ul>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 col-xxl-4">
                <label class="info">
                    User :
                    <span>{{ $record->department?->getDisplayName() }}</span> >
                    {{ $record->assignToUser?->getDisplayName() }}
                </label>
                <label class="info">
                    {{ get_value_from_list($field_labels, 'priority', 'priority') }}
                    :
                    <span>{{ $record->priority }}</span>
                </label>
                <label class="info">
                    {{ get_value_from_list($field_labels, 'tag_name', 'tag_name') }}
                    :
                    <span>{{ implode(', ', $record->tags->pluck('name')->toArray()) }}</span>
                </label>
                <label class="info">
                    {{ get_value_from_list($field_labels, 'status', 'status') }}
                    :
                    <span>{{ get_value_from_list($status_list, $record->status) }}</span>
                </label>
                <label class="info">
                    {{ get_value_from_list($field_labels, 'description', 'description') }}
                    :
                    <span>{!! $record->description !!}</span>
                </label>
                <h5>
                    Link Users
                </h5>
                <ol>
                    @foreach ($record->user as $user)
                        <li>{{ $user->getDisplayName() }}</li>
                    @endforeach
                </ol>
            </div>
            <div class="col-md-6 col-xxl-4">
                <label class="info">
                    {{ get_value_from_list($field_labels, 'duration', 'duration') }} :
                    <span>{{ $record->duration }}
                        {{ get_value_from_list($duration_type_list, $record->duration_type) }}</span>
                </label>
                <x-Backend.celec_date_time
                    label="{{ get_value_from_list($field_labels, 'start_date_time', 'start_date_time') }}"
                    :value="$record->start_date_time" />
                <x-Backend.celec_date_time
                    label="{{ get_value_from_list($field_labels, 'end_date_time', 'end_date_time') }}"
                    :value="$record->end_date_time" />
            </div>
            <div class="col-md-6 col-xxl-4">
                <x-Backend.index-table-info :record="$record" :userList="$userListCache" />
            </div>
        </div>
    </div>
</div>
