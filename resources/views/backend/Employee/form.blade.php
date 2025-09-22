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

    <div class="card">
        <div class="card-header card-no-border pb-0">
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 col-xxl-6">
                    <?php
                    $field = 'type';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$type_list"
                        :value="$model->{$field}"
                        layout="horizontal"
                        class="select2"
                        mandatory="true" />

                    <?php
                    $field = 'name';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                        :value="$model->{$field}" 
                        layout="horizontal"
                        mandatory="true" 
                        placeholder="Enter {{ $label }}" 
                        />

                    <?php
                    $field = 'email';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.text-field type="email" :id="$field" :name="$field" :label="$label"
                        :value="$model->{$field}" 
                        layout="horizontal"
                        placeholder="Enter {{ $label }}" 
                        />

                    <?php
                    $field = 'mobile';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <label class="form-label">
                                {{ $label }}
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <x-Inputs.group-text-field :id="$field" :name="$field" :label="$false" value="{{ $model->{$field} }}"
                                placeholder="Enter {{ $label }}" 
                                layout="horizontal"
                                placeholder="Enter {{ $label }}" 
                                autocomplete="off"
                                secondaryTextPosition="first"
                                secondaryText='<span class="input-group-text">+91</span>'
                                mandatory="true" 
                                />
                        </div>
                    </div>
                                
                    <?php
                    $field = 'mobile2';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <label class="form-label">
                                {{ $label }}
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <x-Inputs.group-text-field :id="$field" :name="$field" :label="$false" value="{{ $model->{$field} }}"
                                placeholder="Enter {{ $label }}" 
                                layout="horizontal"
                                placeholder="Enter {{ $label }}" 
                                autocomplete="off"
                                secondaryTextPosition="first"
                                secondaryText='<span class="input-group-text">+91</span>'
                                />
                        </div>
                    </div>
                        
                    <?php
                    $field = 'whatsapp_mobile';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <label class="form-label">
                                {{ $label }}
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <x-Inputs.group-text-field :id="$field" :name="$field" :label="$false"
                                class="validate-mobile" :value="$model->{$field}" 
                                placeholder="Enter {{ $label }}"
                                layout="horizontal"
                                secondaryText='<span class="input-group-text text-center"><span id="same_as_mobile" class="btn btn-secondary btn-sm">Same As Mobile</span></span>'
                                />
                        </div>
                    </div>
                            
                    <?php
                    $field = 'branch_id';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$branch_list"
                        :value="$model->{$field}"
                        layout="horizontal"
                        class="select2"
                        mandatory="true" />

                    <?php
                    $field = 'department_id';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$dept_list"
                        :value="$model->{$field}"
                        layout="horizontal"
                        class="select2"
                        mandatory="true" />

                    <?php
                    $field = 'designation_id';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                        :list="$desg_list"
                        :value="$model->{$field}"
                        layout="horizontal"
                        class="select2"
                        mandatory="true"
                    />

                    <?php
                    $field = 'current_state_id';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$state_list"
                        :value="$model->{$field}"
                        layout="horizontal"
                        class="select2 cascade-drop-down"
                        data-sr-cascade-target="#current_city_id"
                        data-sr-cascade-url="/city/ajax_get_list/{v}"
                        mandatory="true"
                        />

                    <?php
                    $field = 'current_city_id';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$empty_list"
                        :value="$model->{$field}"
                        layout="horizontal"
                        class="select2"
                        mandatory="true"
                        />

                    <?php
                    $field = 'current_address';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                        :value="$model->{$field}" 
                        layout="horizontal"
                        mandatory="true" 
                        placeholder="Enter {{ $label }}" 
                        />

                    <?php
                    $field = 'current_address2';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                        :value="$model->{$field}" 
                        layout="horizontal"
                        placeholder="Enter {{ $label }}" 
                        />


                    <?php
                    $field = 'permanent_state_id';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$state_list"
                        :value="$model->{$field}"
                        layout="horizontal"
                        class="select2 cascade-drop-down"
                        data-sr-cascade-target="#permanent_city_id"
                        data-sr-cascade-url="/city/ajax_get_list/{v}"
                        mandatory="true"
                        />

                    <?php
                    $field = 'permanent_city_id';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$empty_list"
                        :value="$model->{$field}"
                        layout="horizontal"
                        class="select2"
                        mandatory="true"
                        />

                    <?php
                    $field = 'permanent_address';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                        :value="$model->{$field}" 
                        layout="horizontal"
                        mandatory="true" 
                        placeholder="Enter {{ $label }}" 
                        />

                    <?php
                    $field = 'permanent_address2';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                        :value="$model->{$field}" 
                        layout="horizontal"
                        placeholder="Enter {{ $label }}" 
                        />

                    <?php
                    $field = 'dob';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                        :value="$model->{$field}" 
                        class="select2 date-picker"
                        layout="horizontal"
                        mandatory="true" 
                        placeholder="Enter {{ $label }}" 
                        />

                    <?php
                    $field = 'doj';
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                        :value="$model->{$field}" 
                        class="select2 date-picker"
                        layout="horizontal"
                        mandatory="true" 
                        placeholder="Enter {{ $label }}" 
                        />                        

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3 text-center">
                                @if ($model->photo_url)
                                    <a id="profile_photo_block" class="fancybox"
                                        data-fancybox="group-{{ $model->id }}"
                                        href="{{ $model->photo_url }}">
                                        <img class="img-thumbnail rounded-circle avatar-xl"
                                            src="{{ $model->photo_url }}" />
                                    </a>
                                @endif

                                <input type="hidden" id="photo" name="photo"
                                    value="{{ old('photo') }}" />

                                <span id="modal_crop_opener" class="btn btn-secondary d-block m-2">
                                    Choose Photo
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <x-Inputs.checkbox name="dont_send_email" label="Don't Send SMS" :value="$model->dont_send_sms" />
                                <x-Inputs.checkbox name="dont_send_email" label="Don't Whatsapp Msg" :value="$model->dont_send_whatsapp_msg" />
                                <x-Inputs.checkbox name="is_active" label="Active" :value="$model->is_active" />
                            </div>
                        </div>
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
                            $("#photo").val(response['file']);
                            $("#inline-loader").hide();
                            $("#modal-crop").modal("hide");

                            $.loader.hide();
                        });
                    });
                });
            });



            $.events.execAtLast(function() {
                $("#current_state_id, #permanent_state_id").trigger("change", {
                    pageLoad: true
                });

                $("#same_as_mobile").click(function() {
                    $("#whatsapp_mobile").val($("#mobile").val());
                });
            });
        });
    </script>
@endsection
