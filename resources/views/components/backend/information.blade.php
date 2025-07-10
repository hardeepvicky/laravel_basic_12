<div class="card-wrapper rounded-3 h-100 bg-light-info mb-2">
    <ol class="mb-0">
        @foreach ($info as $arr)
            @if (isset($arr['msg']))
                <li>
                    {{ $arr['msg'] }}
                </li>
            @endif
        @endforeach
    </ol>
</div>
