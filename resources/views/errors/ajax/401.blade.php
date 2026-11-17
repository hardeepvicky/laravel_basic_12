<div class="text-center mb-5">
    <h1 class="display-1 fw-semibold">401</h1>
    <h4 class="text-uppercase">You are not authorize to access this page or feature</h4>
    <h6>
        {{ $current_url }}
    </h6>
    <h6>
        Route : {{ $current_route_name }}
    </h6> 
    <div class="m-2">
        <a id="ajax_request_access" class="btn btn-secondary btn-sm ajaxify" href="{{ route("permissions.ajax_request_access", ["route_name" => $current_route_name]) }}" 
            data-callback="on_request_accesss_sent">
            Request Access
        </a>
    </div>
</div>

<script>
    function on_request_accesss_sent(){
        $("a#ajax_request_access").replaceWith('<label class="badge badge-info">Request Sent, Please wait till Admin give you Permission</label>');
    }
</script>