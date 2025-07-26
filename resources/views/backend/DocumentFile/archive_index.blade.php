@extends($layout)

@section('content')    
    <div class="card">
        <div class="card-header d-md-block d-none card-no-border pb-0">
            <div class="file-content b-b-primary p-2">
                <div class="d-md-flex d-sm-block align-items-center">
                    <div class="flex-grow-1">
                        @include($viewPrefix . ".search_form")
                    </div>                    
                </div>
            </div>
        </div>
        <div class="card-body file-manager">            
            <div id="index_table">
                @include($viewPrefix . '.index_records')
            </div>
        </div>
    </div>
@endsection
