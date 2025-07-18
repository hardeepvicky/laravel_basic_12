<ul class="d-flex flex-row files-content">
    @foreach($records as $record)
    <li class="folder-box d-flex align-items-center">
        <div class="d-flex align-items-center files-list">
            <div class="flex-shrink-0 file-left"> 
                <i class="{{ $record->font_awesome_icon }} text-info fs-4"></i>
            </div>
            <div class="flex-grow-1 ms-3">
                <h5 class="f-w-600">{{ $record->name }}</h5>
                <p>
                    {{ $record->updated_at->diffForHumans() }} ,

                    {{ round($record->file_size_in_mb, 2) }} MB
                </p>
            </div>
        </div>
    </li>
    @endforeach
</ul>