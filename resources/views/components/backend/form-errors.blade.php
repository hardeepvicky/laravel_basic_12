@if ($errors->any())

    <div class="card-wrapper rounded-3 h-100 bg-light-secondary mb-2">
        <h6 class="sub-title f-w-600">Errors</h6>
        <ol class="mb-0">
            @foreach ($errors->all() as $data)
                @if (is_array($data))
                    <li>
                        <b>{{ $data[0]['group'] ?? '' }}</b>
                    </li>
                    <ol>
                        @foreach ($data as $error)
                            @if (isset($error['label']) && isset($error['msg']))
                                <li>
                                    {{ $error['label'] ? $error['label'] . ' : ' : '' }} {{ $error['msg'] }}
                                </li>
                            @endif
                        @endforeach
                    </ol>
                @else
                    <li>{{ $data }}</li>
                @endif
            @endforeach
        </ol>
    </div>

@endif
