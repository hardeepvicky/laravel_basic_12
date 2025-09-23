<<<<<<< HEAD
@if ($errors->any())

<div class="card-wrapper rounded-3 h-100 bg-light-secondary">
    <h6 class="sub-title f-w-600">Errors</h6>
    <ol class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ol>
</div>

@endif
=======
<?php

use Illuminate\Support\Facades\Session;

if (Session::has("custom_errors"))
{
    $customErrors = Session::get("custom_errors");
    Session::forget('custom_errors');
}
?>

@if ($errors->any() || (isset($customErrors) && $customErrors))
    <div class="card-wrapper rounded-3 h-100 bg-light-secondary mb-2">
        <h6 class="sub-title f-w-600">Errors</h6>
        <ol class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach

            @foreach ($customErrors as $group => $errors)
                @if (is_array($errors))
                    @if(is_string($group))
                    <li>
                        <b>{{ $group }}</b>
                    </li>
                    @endif
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
>>>>>>> base_v3
