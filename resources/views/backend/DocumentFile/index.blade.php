@extends($layout)

@section('content')
    <div class="card">
        <div class="card-header d-md-block d-none card-no-border pb-0">
            <div class="file-content b-b-primary p-2">
                <div class="d-md-flex d-sm-block align-items-center">
                    <div class="flex-grow-1">
                        @include($viewPrefix . ".search_form")
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <form class="d-inline-flex">
                            <span class="btn btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#modal-add-via-url">
                                Add Via URL
                            </span>

                            <span class="btn btn-outline-primary ms-2" data-bs-toggle="modal"
                                data-bs-target="#modal-add-via-upload">
                                Add Via Upload
                            </span>
                        </form>
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
