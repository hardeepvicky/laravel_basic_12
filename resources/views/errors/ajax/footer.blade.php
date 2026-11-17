@if (isset($contact_support_email))
    <div class="mt-8 text-gray-600 text-sm text-center">
        If you believe this is an error, please
        <a href="mailto:{{ $contact_support_email }}" class="text-indigo-600 hover:text-indigo-800 underline">contact
            support</a>.
    </div>
@endif
