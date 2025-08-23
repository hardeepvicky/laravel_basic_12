    <div class="file-content">
        <ul class="d-flex flex-row">
            @foreach ($records as $record)
                <li class="folder-box d-flex align-items-center">
                    <div class="file-block p-2" data-id="{{ $record->id }}" style="width:500px">
                        <div class="d-flex align-items-center files-list">
                            <div class="flex-shrink-0">
                                @if (isset($record->mainImage) && $record->mainImage)
                                    <a class="fancybox image-container" data-fancybox="image" href="{{ $record->mainImage->file_url }}">
                                        <img class="small-img lazy-load-image" style="max-width: 150px;" src="{{ $record->mainImage->file_url }}" alt="{{ $record->mainImage->name }}" />
                                    </a>
                                @else
                                    <div class="file-left">
                                        <i class="fa-solid fa-file-image text-info fs-3"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1 d-flex align-items-center ml-1">
                                <div class="flex-grow-1">
                                    <h6 class="f-w-600">
                                        Name : {{ str_short_with_dot($record->name) }}
                                    </h6>
                                    <h6 class="f-w-600">
                                        Model Name : {{ str_short_with_dot($record->model_name) }}
                                    </h6>
                                    <h6 class="f-w-600">
                                        Net Dimension :
                                        <span>
                                            {{ $record->length }} x {{ $record->width }} x {{ $record->height }}
                                            {{ $record->size_uom_name }}
                                        </span>
                                    </h6>
                                    <h6 class="f-w-600">
                                        Net Weight : {{ $record->net_weight }} Kg.
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
