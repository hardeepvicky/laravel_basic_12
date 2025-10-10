<ul class="d-flex flex-row">
@foreach ($records as $record)
    <li>
        <div class="m-2" data-id="{{ $record->id }}">
            <h6 class="f-w-600">
                {{ $record->name }}
            </h6>
        </div>

    </li>
@endforeach
</ul>
