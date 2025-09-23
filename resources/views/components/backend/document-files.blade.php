<?php
use App\Helpers\LaravelExtend;
?>

<div class="file-content mt-2">
<ul class="d-flex flex-row files-content">
    @foreach($records as $record)
    <?php
        $mime_type = get_mime_type_from_extension($record->file_ext);
    ?>
    <li class="folder-box d-flex align-items-center">
        @if($record->file_url && in_array($record->type, ["image", "video", "audio"]))
            <div class="file-block card " data-id="{{ $record->id }}">
                <i class="fas fa-check check-icon"></i>
                <div class="card-header">
                    @if($record->type =="image")
                        <a href="{{ $record->file_url }}" class="fancybox image-container" data-fancybox="image">
                            <img class="full-img lazy-load-image" src="{{ $record->file_url }}" alt="{{ $record->file_name }}"/>
                            <div class="image-loader"></div>
                            <div class="error-icon" aria-hidden="true">&#x26A0;</div>
                        </a>
                    @elseif($record->type =="video")
                        <video class="full-video" controls src="{{ $record->file_url }}"></video>
                    @elseif($record->type =="audio")
                        <audio controls class="full-audio">
                            <source src="{{ $record->file_url }}" type="{{ $mime_type }}">
                            Your browser does not support the audio element.
                        </audio>
                    @endif
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="f-w-600">{{ str_short_with_dot($record->name) }}</h5>
                            <p>
                                {{ round($record->file_size_in_mb, 2) }} MB
                            </p>
                        </div>
                        <div class="flex-shrink-0 text-end">

                        </div>
                    </div>
                </div>
            </div>
        @else
        <div class="file-block p-2" data-id="{{ $record->id }}">
            <i class="fas fa-check check-icon"></i>
            <div class="d-flex align-items-center files-list">
                <div class="flex-shrink-0 file-left">
                    <i class="{{ $record->font_awesome_icon }} text-info fs-4"></i>
                </div>
                <div class="flex-grow-1 d-flex align-items-center ml-1">
                    <div class="flex-grow-1">
                        <h5 class="f-w-600">{{ str_short_with_dot($record->name) }}</h5>
                        <p>
                            {{ round($record->file_size_in_mb, 2) }} MB                            
                        </p>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <?php
                    $url = $record->file_url ?? $record->url;
                ?>
                <a target="_blank" href="{{ $url }}" class="btn btn-secondary btn-sm btn-round">
                    Open
                </a>
            </div>
        </div>
        @endif
    </li>
    @endforeach
</ul>
</div>