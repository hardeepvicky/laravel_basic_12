<?php
use App\Helpers\LaravelExtend;
?>

<x-Backend.pagination-links :records="$records" />
<div class="file-content mt-2">
<ul class="d-flex flex-row files-content">
    @foreach($records as $record)
    <?php
        $mime_type = get_mime_type_from_extension($record->file_ext);

        $file_block_active_css = "";

        if (isset($selected_id_list))
        {
            if (in_array($record->id, $selected_id_list))
            {
                $file_block_active_css = "active";
            }
        }
    ?>    
    <li class="folder-box d-flex align-items-center">
        @if($record->file_url && in_array($record->type, ["image", "video", "audio"]))
            <div class="file-block card {{ $file_block_active_css }}" data-id="{{ $record->id }}">
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
                            <h5 class="f-w-600">
                                <a class="x-editable" href="javascript:void();"data-type="text"
                                    data-name="name"
                                    data-pk="{{ $record->id }}"
                                    data-url="{{ route($routePrefix . '.ajax_update_field') }}"
                                    data-title="Enter Name">{{ str_short_with_dot($record->name) }}</a>
                            </h5>
                            <span>By : {{ LaravelExtend::getCreatedByOrUpdatedBy($record->created_by) }}</span>
                            <p style="font-size: 12px">
                                {{ $record->created_at_user_tz->diffForHumans() }} ,
                                {{ round($record->file_size_in_mb, 2) }} MB
                            </p>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <?php
                                $url = $record->file_url ?? $record->url;
                            ?>
                            <a download="{{ $record->name }}" href="{{ $url }}" class="btn btn-outline-secondary summary-action-button">
                                <i class="fa-solid fa-download label-icon"></i>
                            </a>
                            @if($record->is_archive)
                                <a href="{{ route($routePrefix .'.unarchive', ['id' => $record->id]) }}"
                                    class="btn btn-outline-success summary-action-button document-file-unarchive"
                                    >
                                    <i class="fa-solid fa-trash-can-arrow-up label-icon"></i>
                                </a>
                            @else
                                <a href="{{ route($routePrefix .'.archive', ['id' => $record->id]) }}"
                                    class="btn btn-outline-danger summary-action-button document-file-archive"
                                    >
                                    <i class="<?= ICON_SUMMARY_DELETE ?> label-icon"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
        <div class="file-block  {{ $file_block_active_css }} p-2" data-id="{{ $record->id }}">
            <i class="fas fa-check check-icon"></i>
            <div class="d-flex align-items-center files-list">
                <div class="flex-shrink-0 file-left">
                    <i class="{{ $record->font_awesome_icon }} text-info fs-4"></i>
                </div>
                <div class="flex-grow-1 d-flex align-items-center ml-1">
                    <div class="flex-grow-1">
                        <h5 class="f-w-600">
                            <a class="x-editable" href="javascript:void();"data-type="text"
                                data-name="name"
                                data-pk="{{ $record->id }}"
                                data-url="{{ route($routePrefix . '.ajax_update_field') }}"
                                data-title="Enter Name">{{ str_short_with_dot($record->name) }}</a>
                        </h5>
                        <span>By : {{ LaravelExtend::getCreatedByOrUpdatedBy($record->created_by) }}</span>
                        <p style="font-size: 12px">
                            {{ $record->created_at_user_tz->diffForHumans() }}
                            @if ($record->file)
                                , {{ round($record->file_size_in_mb, 2) }} MB
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <?php
                    $url = $record->file_url ?? $record->url;
                ?>
                <a target="_blank" href="{{ $url }}" class="btn btn-outline-info summary-action-button">
                    <i class="fa-solid fa-eye label-icon"></i>
                </a>
                <a download="{{ $record->name }}" href="{{ $url }}" class="btn btn-outline-secondary summary-action-button">
                    <i class="fa-solid fa-download label-icon"></i>
                </a>

                @if($record->is_archive)
                    <a href="{{ route($routePrefix .'.unarchive', ['id' => $record->id]) }}"
                        class="btn btn-outline-success summary-action-button document-file-unarchive"
                        >
                        <i class="fa-solid fa-trash-can-arrow-up label-icon"></i>
                    </a>
                @else
                    <a href="{{ route($routePrefix .'.archive', ['id' => $record->id]) }}" class="btn btn-outline-danger summary-action-button document-file-archive">
                        <i class="<?= ICON_SUMMARY_DELETE ?> label-icon"></i>
                    </a>
                @endif
            </div>
        </div>
        @endif
    </li>
    @endforeach
</ul>
</div>

<x-Backend.pagination-links :records="$records" />
<script>
    $(".file-block").click(function() {

        var multiple = '<?= $multiple ?>';

        if (multiple == "1")
        { 
            $(this).toggleClass("active");
        }
        else
        {
            $(".file-block").removeClass("active");

            $(this).addClass("active");
        }
    });
</script>