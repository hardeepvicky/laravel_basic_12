<div class="d-flex justify-content-center">
    <div class="card error-card">
        <!-- Error Code -->
        <div class="error-code">
            {{ $error_code }}
        </div>

        <!-- Error Title -->
        <h4 class="error-title">
            {{ $error_title }}
        </h4>

        <!-- Error Description -->
        <p class="error-description mb-2">
            {{ $error_msg }}

            <x-Backend.form-errors />
        </p>

        @if(isset($file_name) && isset($file_line_no))
        <div class="info-for-developer mb-2">
            <div>
                File : {{ $file_name }}
            </div>
            <div class="mt-2">
                File Line No. : {{ $file_line_no }}
            </div>
            <div class="mt-2">
                URL : {{ $current_url }}
            </div>
        </div>
        @endif
        
        {!! $html !!}

        <div class="help-links text-center mt-1 mb-2">
            @if (is_ajax_request())
            @else
                <span class="btn btn-secondary" onclick="history.back();">Back</span>
            @endif
        </div>

        @if(isset($history) && is_array($history) && count($history) > 0)
        @foreach($history as $arr)
        <div class="info-for-developer">            
            {!! array_print($arr, 255) !!}
        </div>
        @endforeach
        @endif
    </div>
</div>
