<div class="g-recaptcha" data-sitekey="{{ $google_cacha_key }}"></div>

@if ($errors->has('g-recaptcha-response'))
    <span class="text-danger">{{ $errors->first('g-recaptcha-response') }}</span>
@endif