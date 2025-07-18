@extends($layout)

@section('content')
    <div class="file-content">
        <div class="card">
            <div class="card-header d-md-block d-none card-no-border pb-0">
                <div class="d-md-flex d-sm-block align-items-center">
                    <form class="form-inline basic-form" action="#" method="get">
                        <div class="form-group d-flex mb-0 align-items-center"><i class="fa-solid fa-magnifying-glass"></i>
                            <input class="form-control-plaintext" type="text" placeholder="Search...">
                        </div>
                    </form>
                    <div class="flex-grow-1 text-end">
                        <form class="d-inline-flex">
                            <button class="btn btn-outline-primary">
                                Add Via URL
                            </button>

                            <span class="btn btn-outline-primary ms-2" data-bs-toggle="modal"
                                data-bs-target="#modal-add-via-upload">
                                Add Via Upload
                            </span>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body file-manager">
                <h4 class="mb-2">Quick Access</h4>
                <ul class="quick-file d-flex flex-row">
                    <li>
                        <div class="quick-box"><i class="fa-brands fa-youtube text-danger"></i></div>
                        <h6 class="mb-2">Videos</h6>
                    </li>
                    <li>
                        <div class="quick-box"><i class="fa-solid fa-music text-warning"> </i></div>
                        <h6 class="mb-2">Music</h6>
                    </li>
                    <li>
                        <div class="quick-box"><i class="fa-solid fa-file text-secondary"></i></div>
                        <h6 class="mb-2">Document</h6>
                    </li>
                </ul>
                <h4 class="mt-4 mb-2 mb-2 mb-2">Files</h4>

                @include($viewPrefix . ".index_records")
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-add-via-upload" tabindex="-1" aria-labelledby="modal-add-via-upload"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5">Add Via Upload</h3>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary submit" type="button">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            $.events.execAtLast(function() {

                $('#modal-add-via-upload').on('show.bs.modal', function() {

                    var form_url = "/document-file/create?add_via=upload";

                    $('#modal-add-via-upload').find(".modal-body").html("Loading....");

                    $('#modal-add-via-upload').find(".modal-body").load(form_url, function() {
                        $('#modal-add-via-upload').find(".modal-body form").ajaxForm({
                            beforeSubmit: function(arr, $form, options) { 

                                $.loader.init().setInfo("Uploading....").show();
                                
                                return true;
                            },
                            uploadProgress : function(event, position, total, percentComplete)
                            {
                                $.loader.setInfo("Uploading " + percentComplete + "%");
                            },
                            error : function()
                            {
                                $.loader.hide();
                            },
                            success : function(responseText, statusText, xhr)
                            {
                                ajaxHandleResponse(form_url, responseText, function(response)
                                {
                                    $('#modal-add-via-upload').modal("hide");
                                })
                            }
                        });
                    });
                });

                $('#modal-add-via-upload .submit').click(function() {
                    $('#modal-add-via-upload form').trigger("submit");
                });
            });
        });
    </script>
@endsection
