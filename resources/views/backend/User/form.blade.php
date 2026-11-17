<?php
use App\Models\User;
?>

@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];
    $empty_list = [];
    ?>

    <style>
        #crop_image_area {
            width: 100%;
            max-width: 400px;
            height: 350px;
            margin: 0 auto 50px auto;
            border: 1px solid;
            padding: 2px;
        }

        #crop_image_preview {
            display: none;
            margin: 5px auto 5px auto;
            width: 200px;
            height: 200px;
        }
    </style>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <x-Form.start action="{{ $form['url'] }}" method="{{ $form['method'] }}" class="i-validate" />
    <x-Inputs.text-field type="hidden" name="country_id" :value="$model->country_id" />

    <div class="card">
        <div class="card-header card-no-border pb-0">
        </div>
        <div class="card-body">          
                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <div class="form-group mb-3">
                            <?php
                            $field = 'type';
                            $label = 'Type';
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$type_list"
                                :value="$model->{$field}" class="select2" mandatory="true" />
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="form-group mb-3">
                            <?php
                            $field = 'name';
                            $label = 'Name';
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}" mandatory="true" placeholder="Enter {{ $label }}" />
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="form-group mb-3">
                            <?php
                            $field = 'email';
                            $label = 'Email';
                            ?>
                            <x-Inputs.text-field type="email" :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}" mandatory="true" placeholder="Enter {{ $label }}" />
                            <div class="help-block">This will use as login id</div>
                        </div>
                    </div>
                </div>

                @if ($is_add_form)
                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'password';
                                $label = 'Password';
                                ?>
                                <x-Inputs.text-field type="password" :id="$field" :name="$field"
                                    :label="$label" mandatory="true" placeholder="Enter {{ $label }}" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'password_confirm';
                                $label = 'Password Confirm';
                                ?>
                                <x-Inputs.text-field type="password" :id="$field" :name="$field"
                                    :label="$label" mandatory="true" placeholder="Enter {{ $label }}" />
                            </div>
                        </div>
                    </div>
                @endif                    
                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <div class="form-group mb-3">
                            <?php
                            $field = 'mobile';
                            $label = 'Mobile';
                            ?>
                            <x-Inputs.group-text-field :id="$field" :name="$field" :label="$label"
                                :value="$model->{$field}" class="validate-mobile" placeholder="Enter {{ $label }}"
                                mandatory="true"
                                secondaryText='<span id="mobile_country_code" class="input-group-text">+91</span>' />
                        </div>
                    </div>                       
                </div>                    
                <div class="row">
                    <div class="col-md-12 col-lg-6">
                    <?php
                    $value = implode(',', $model->userRole->pluck('role_id')->toArray());
                    ?>
                    <x-Inputs.drop-down name="roles[]" label="Roles" :list="$role_list" :value="$value"
                        class="select2" multiple="multiple" mandatory="true" />
                </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <div class="form-group mb-3 text-center">
                            @if ($model->profile_image_url)
                                <a id="profile_photo_block" class="fancybox"
                                    data-fancybox="group-{{ $model->id }}"
                                    href="{{ $model->profile_image_url }}">
                                    <img class="img-thumbnail rounded-circle avatar-xl"
                                        src="{{ $model->profile_image_url }}" />
                                </a>
                            @endif

                            <input type="hidden" id="profile_image" name="profile_image"
                                value="{{ old('profile_image') }}" />

                            <span id="modal_crop_opener" class="btn btn-secondary d-block m-2">
                                Choose Profile Photo
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="form-group mb-3">
                            <x-Inputs.checkbox name="dont_send_email" label="Don't Send Email" :value="$model->dont_send_email" />
                            <x-Inputs.checkbox name="is_active" label="Active" :value="$model->is_active" />
                        </div>
                    </div>
                </div>


        </div>
        <x-Backend.form-common-footer-buttons />
    </div>
    </form>

    <div class="modal fade" id="modal-crop" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5">Upload & Crop Photo</h3>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="d-flex justify-content-between">
                        <div id="crop_image_area" class="border-primary"></div>
                        <div id="crop_image_preview">
                            <img />
                        </div>
                    </div>

                    <input type="file" name="file" id="crop_image_file" class="hidden" />
                    <span id="crop_image_file_opener" class="btn btn-secondary">Choose Photo</span>

                    <span id="crop_btn" class="btn btn-secondary">Crop</span>

                </div>
                <div class="modal-footer">
                    <span id="crop_and_upload_btn" class="btn btn-primary">Crop & Upload</span>
                    <div id="inline-loader" class="spinner-border text-primary m-1" style="display: none;">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(function() {
            $("#modal_crop_opener").click(function() {
                $("#modal-crop").modal("show");
            });

            $("#crop_image_file_opener").click(function() {
                $("input#crop_image_file").click();
            });

            var $uploadCrop = $('#crop_image_area').croppie({
                viewport: {
                    width: 200,
                    height: 200,
                    type: 'circle'
                },
                enableExif: true
            });

            function get_file_name() {
                var fullPath = $('input#crop_image_file').val();
                var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf(
                    '/'));
                var filename = fullPath.substring(startIndex);
                if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
                    filename = filename.substring(1);
                }

                return filename;
            }

            function readFile(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {

                        $uploadCrop.croppie('bind', {
                            url: e.target.result
                        }).then(function() {

                        });

                    }

                    reader.readAsDataURL(input.files[0]);
                } else {
                    alert("Sorry - you're browser doesn't support the FileReader API");
                }
            }

            $('input#crop_image_file').on('change', function() {
                readFile(this);
            });

            $("#crop_btn").click(function() {
                $uploadCrop.croppie('result', {
                    type: 'canvas',
                    size: 'viewport'
                }).then(function(resp) {
                    $("#crop_image_preview img").attr("src", resp);
                    $("#crop_image_preview").show();
                    $("#profile_photo_block").hide();
                });
            });

            $("#crop_and_upload_btn").click(function() {
                $uploadCrop.croppie('result', {
                    type: 'canvas',
                    size: 'viewport'
                }).then(function(resp) {
                    $("#crop_image_preview img").attr("src", resp);
                    $("#crop_image_preview").show();
                    $("#profile_photo_block").hide();

                    var url = '/public/ajax_upload_base64';
                    var data = {
                        "base64": resp,
                        "filename": get_file_name()
                    };

                    $("#inline-loader").show();

                    $.loader.setInfo("Loading...").show();

                    $.post(url, data, function(responseText) {
                        console.log(responseText);
                        ajaxHandleResponse(url, responseText, function(response) {
                            $("#profile_image").val(response['file']);
                            $("#inline-loader").hide();
                            $("#modal-crop").modal("hide");

                            $.loader.hide();
                        });
                    });
                });
            });

            // $.events.execAtLast(function() {
                
            // });
        });
    </script>
@endsection
