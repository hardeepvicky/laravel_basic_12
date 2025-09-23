@extends($layout)

@section('content')
<<<<<<< HEAD

<?php
    $page_header_links = [
        ["title" => "Summary", "url" => route($routePrefix . ".index")]
    ];
?>

@include($partial_path . ".page_header")

<style>
    .first-row
    {
        border-top: 2px solid #4c667f !important;
    }
</style>

<div class="card">
    <div class="card-body">

        <form action="{{ route($routePrefix . '.assign') }}" method="POST">
            <div class="row">
                <div class="col-lg-12">
                    {!! csrf_field() !!}
                    <div class="row mb-4">
                        <label class="col-md-2 col-sm-23 col-form-label" style="text-align:right;">Role</label>
                        <div class="col-md-6 col-sm-12">
                            <select id="role_id" name="role_id" class ="form-control select2" required="required">
                                <option value="">Please Select</option>
                                @foreach($role_list as $k => $t)
                                    <option value="{{ $k }}">{{$t}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div id="permission_block"></div>
                    </div>                    
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function()
    {
        $("select#role_id").change(function()
        {
            var id = $(this).val();

            var v = $(this).val();
            v = v ? v : 0;

            if ($("#permission_block").not(":empty").length > 0)
            {
                Swal.fire({
                    title: "Are you sure?",
                    text: "You have change permissions but did not save it, Are you sure to ignore changes",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: constants.swal.button.confirm_color,
                    cancelButtonColor: constants.swal.button.cancel_color,
                    confirmButtonText: "Yes"
                }).then(function(e) {
                    if (e.value)
                    {
                        $("#permission_block").html("");
                        if (v)
                        {
                            $.loader.show();
                            $("#permission_block").load("/permissions/ajax_get_permissions/" + v, () => {
                                $.loader.hide();
                            });
                        }
                    }
                });
            }
            else
            {
                $("#permission_block").html("");
                if (v)
                {
                    $.loader.show();
                    $("#permission_block").load("/permissions/ajax_get_permissions/" + v, () => {
                        $.loader.hide();
                    });
                }
            }
        });

        $("form").submit(function()
        {
            var len = $("input.aco_action").length;

            if (len <= 0)
            {
                $("select#role_id").trigger("change");
                return false;
            }

            var check_len = $("input.aco_action:checked").length;

            if (check_len <= 0)
            {
                Swal.fire(
                    'Error!',
                    'Please Select At Least One Checkbox',
                    'error'
                )

                return false;
            }
        });
    });
</script>

=======
    <?php
    $page_header_links = [['title' => 'Summary', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <style>
        .first-row {
            border-top: 2px solid #4c667f !important;
        }

        
    </style>

    <div class="card">
        <div class="card-body">

            <form action="{{ route($routePrefix . '.assign') }}" method="POST">
                <div class="row">
                    <div class="col-lg-12">
                        {!! csrf_field() !!}
                        <div class="row mb-4">
                            <label class="col-md-2 col-sm-6 col-form-label" style="text-align:right;">Role</label>
                            <div class="col-md-10 col-sm-6">
                                <select id="role_id" name="role_id[]" class ="form-control select2" required="required" multiple="true">
                                    <option value="">Please Select</option>
                                    @foreach ($role_list as $k => $t)
                                        <option value="{{ $k }}">{{ $t }}</option>
                                    @endforeach
                                </select>

                                <span class="btn btn-primary" id="get_permissions">Search</span>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div id="permission_block"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {

            $("#get_permissions").click(function() {

                var v = $("#role_id").val();
                v = v ? v : [];

                if (v.length == 0)
                {
                    return;
                }

                if ($("#permission_block").not(":empty").length > 0) {
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You have change permissions but did not save it, Are you sure to ignore changes",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: constants.swal.button.confirm_color,
                        cancelButtonColor: constants.swal.button.cancel_color,
                        confirmButtonText: "Yes"
                    }).then(function(e) {
                        if (e.value) {
                            $("#permission_block").html("");
                            if (v) {
                                $.loader.show();
                                $("#permission_block").load("/permissions/ajax_get_permissions", {role_ids : v}, function(){
                                    $.loader.hide();
                                });
                            }
                        }
                    });
                } else {
                    $("#permission_block").html("");
                    if (v) {
                        $.loader.show();
                        $("#permission_block").load("/permissions/ajax_get_permissions", {role_ids : v}, function(){
                            $.loader.hide();
                        });
                    }
                }
            });

            $("form").submit(function() {

                var v = $("#role_id").val();

                if (v.length == 0)
                {
                    Swal.fire(
                        'Error!',
                        'Please Select At One Role',
                        'error'
                    )
                    return false;
                }

                var len = $("#hidden_routes input").length;

                if (len <= 0) {
                    Swal.fire(
                        'Error!',
                        'Please Select At Least One Checkbox',
                        'error'
                    )
                    return false;
                }
            });

            $('form').on('keydown', function(event) {
                // Check if the pressed key is the Enter key (keyCode 13 or event.key 'Enter')
                if (event.keyCode === 13 || event.key === 'Enter') {
                    event.preventDefault(); // Prevent the default form submission                    
                    console.log("Enter key pressed. Form submission prevented.");
                    // Optionally, you could focus the next input or trigger other logic here.
                }
            });
        });
    </script>
>>>>>>> base_v3
@endsection
