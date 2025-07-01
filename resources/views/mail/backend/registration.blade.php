<div style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2">
    <div style="margin:50px auto;width:70%;padding:20px 0">
        <div style="border-bottom:1px solid #eee">
            <a href=""
                style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">{{ $company_name }}</a>
        </div>
        <p style="font-size:1.1em">Hi, {{ $name }}</p>

        <a href="{{ $verify_link }}" target="_blank">Click here to go to verify Email</a>

        <p style="color: #00466a;">Company Email : {{ $company_email }}</p>
    </div>
</div>
