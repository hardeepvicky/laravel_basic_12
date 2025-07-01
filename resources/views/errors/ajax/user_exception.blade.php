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

            {!! $html !!}
        </div>

        <div class="help-links text-center">
            @if (is_ajax_request())
            @else
                <span class="btn btn-secondary" onclick="history.back();">Back</span>
            @endif
        </div>
    </div>
</div>
