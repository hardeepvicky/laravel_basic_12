<div class="d-flex justify-content-center">
    <div class="error-card">
        <!-- Error Code -->
        <div class="error-code">
            {{ $error_code }}
        </div>

        <!-- Error Title -->
        <h1 class="error-title">
            Error : {{ $error_msg }}
        </h1>

        <!-- Error Description -->
        <p class="error-description mb-2">
            {!! $html !!}
        </p>

        <div class="info-for-developer">
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
    </div>
</div>
