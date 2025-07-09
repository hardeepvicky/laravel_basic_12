<?php

use App\Helpers\LaravelExtend;

if (LaravelExtend::$warnings):
?>

    <div class="card-wrapper rounded-3 h-100 bg-light-secondary mb-2">
        <h6 class="sub-title f-w-600">Warnings</h6>
        <ol class="mb-0">
            @foreach (LaravelExtend::$warnings as $group => $inner_arr)
                    @if (is_array($inner_arr))
                    <li>
                        <b>{{ $group }}</b>
                    </li>
                    <ol>
                        @foreach ($inner_arr as $arr)
                            @if (isset($arr['label']) && isset($arr['msg']))
                                <li>
                                    {{ $arr['label'] ? $arr['label'] . ' : ' : '' }} {{ $arr['msg'] }}
                                </li>                            
                            @endif
                        @endforeach
                    </ol>
                @endif
            @endforeach
        </ol>
    </div>
<?php endif; ?>
