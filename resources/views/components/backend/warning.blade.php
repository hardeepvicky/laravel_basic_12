<?php

use Illuminate\Support\Facades\Session;

$custom_errors = [];
if (Session::has("custom_errors"))
{
    $custom_errors = Session::get("custom_errors");
    Session::forget('custom_errors');
}
?>

@if ($errors->any() || $custom_errors)
    <div class="card-wrapper rounded-3 h-100 bg-light-secondary mb-2">
        <h6 class="sub-title f-w-600">Errors</h6>
        <ol class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach

            @foreach ($custom_errors as $group => $errors)
                    @if (is_array($errors))
                    <li>
                        <b>{{ $group }}</b>
                    </li>
                    <ol>
                        @foreach ($errors as $error)
                            @if (isset($error['label']) && isset($error['msg']))
                                <li>
                                    {{ $error['label'] ? $error['label'] . ' : ' : '' }} {{ $error['msg'] }}
                                </li>
                            @endif
                        @endforeach
                    </ol>
                @endif
            @endforeach
        </ol>
    </div>

@endif
