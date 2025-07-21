@extends($layout)

@section('content')
    <?php
    $false = false;
    $paginationList = [
        10 => 10,
        15 => 15,
        20 => 20,
        25 => 25,
        30 => 30,
        40 => 40,
        50 => 50,
    ];
    ?>
    <div class="card">
        <div class="card-header d-md-block d-none card-no-border pb-0">
            <div class="file-content b-b-primary p-2">
                <div class="d-md-flex d-sm-block align-items-center">
                    <div class="flex-grow-1">
                        <form method="GET" class="summary_search" action="{{ route($current_route_name) }}">
                            <input type="hidden" name="is_sort_clear" value="1" />
                            <div class="row">
                                <div class="col-md-1 mb-2">
                                    <?php
                                    $field = 'pagination_limit';
                                    ?>
                                    <x-Inputs.drop-down :name="$field" label="Limit" :list="$paginationList" :value="$pagination_limit" :pleaseSelect="$false"
                                        class="pagination_limit" />
                                </div>
                                <div class="col-md-6 col-xxl-3 mb-2">
                                    <?php 
                                        $field = "type";
                                        $label = get_value_from_list($field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$type_list" class="select2" />
                                </div>
                                <div class="col-md-6 col-xxl-3 mb-2">
                                    <?php 
                                        $field = "document_category_id";
                                        $label = get_value_from_list($field_labels, $field, $field);
                                        $category_list = [0 => "-"] + $category_list;
                                    ?>
                                    <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$category_list" class="select2" />

                                    <?php 
                                        $field = "is_find_nested_child";
                                    ?>
                                    <x-Inputs.checkbox :name="$field" label="Find Nested Child" :value="$search[$field]" />
                                </div>
                                <div class="col-md-3 col-xxl-2 mb-2">
                                    <?php
                                    $field = 'name';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]" />
                                </div>
                            </div>
                            <x-Backend.summary-search-form-footer :showPaginationDropDown="$false" />
                        </form>
                    </div>
                    <div class="flex-shrink-0 text-end">
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
        </div>
        <div class="card-body file-manager">            
            <div id="index_table">
                @include($viewPrefix . '.index_records')
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
                            uploadProgress: function(event, position, total,
                                percentComplete) {
                                $.loader.setInfo("Uploading " +
                                    percentComplete + "%");
                            },
                            error: function() {
                                $.loader.hide();
                            },
                            success: function(responseText, statusText, xhr) {
                                ajaxHandleResponse(form_url, responseText,
                                    function(response) {
                                        $('#modal-add-via-upload').modal(
                                            "hide");
                                    })
                            }
                        });
                    });
                });

                $('#modal-add-via-upload .submit').click(function() {
                    $('#modal-add-via-upload form').trigger("submit");
                });
            });

            $(document).on("click", "a.document-file-archive", function(){
                var _li = $(this).closest("li.folder-box");

                var url = $(this).attr("href");

                ajaxGetJson(url, function(response){
                    _li.remove();
                });

                return false;
            });

            $(document).on("click", ".file-block", function(){                
                $(this).toggleClass("active");
            });
        });
    </script>
@endsection
