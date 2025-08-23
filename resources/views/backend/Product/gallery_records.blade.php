<?php
use App\Helpers\LaravelExtend;
?>

<x-Backend.pagination-links :records="$records" />
<div class="file-content mt-2">
<ul class="d-flex flex-row files-content">
    @foreach($records as $record)
    <?php
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
        <div class="file-block  {{ $file_block_active_css }} p-2" data-id="{{ $record->id }}" style="width:400px">
            <i class="fas fa-check check-icon"></i>
            <div class="d-flex align-items-center files-list">
                <div class="flex-shrink-0">
                    @if (isset($record->mainImage) && $record->mainImage)
                        <img class="small-img lazy-load-image" style="max-width: 150px;" src="{{ $record->mainImage->file_url }}" alt="{{ $record->mainImage->name }}"/>
                    @else
                        <div class="file-left">
                            <i class="fa-solid fa-file-image text-info fs-3"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-grow-1 d-flex align-items-center ml-1">
                    <div class="flex-grow-1">
                        <h6 class="f-w-600">
                            {{ str_short_with_dot($record->name) }}
                        </h6>
                        <h6 class="f-w-600">
                            Model Name : {{ str_short_with_dot($record->model_name) }}
                        </h6>
                        <p style="font-size: 12px">
                            By : {{ LaravelExtend::getCreatedByOrUpdatedBy($record->created_by) }}
                            , {{ $record->created_at_user_tz->diffForHumans() }}                            
                        </p>
                    </div>
                </div>
            </div>
        </div>
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