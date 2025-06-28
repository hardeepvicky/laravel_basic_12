<?php
use App\Models\User;
?>

@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];
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

    <div class="row">
        <div class="col-md-12 offset-lg-1 col-lg-10">
            <div class="card">
                <div class="card-header card-no-border pb-0">
                    <h3>Basic</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <x-Inputs.drop-down id="type" name="type" label="Type" :list="$type_list"
                                    :value="$model->type" mandatory="true" class="select2 cascade-drop-down"
                                    data-sr-cascade-target="#user_category_id"
                                    data-sr-cascade-url="/user-category/ajax_get_list/{v}" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <x-Inputs.drop-down id="user_category_id" name="user_category_id" label="Category"
                                    :value="$model->user_category_id" class="select2" mandatory="true" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 type-block type-block-{{ User::TYPE_EXTERNAL }}">
                            <div class="form-group mb-3">
                                <x-Inputs.drop-down id="country_id" name="country_id" label="Country" :list="$country_list"
                                    :value="$model->country_id" class="select2" mandatory="true" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <x-Inputs.text-field id="name" name="name" label="Name" :value="$model->name"
                                    placeholder="Enter Name" mandatory="true" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 type-block type-block-{{ User::TYPE_INTERNAL }}">
                            <div class="form-group mb-3">
                                <x-Inputs.text-field name="person_name" label="Person Name" :value="$model->person_name"
                                    placeholder="Enter Person Name" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <x-Inputs.text-field type="email" name="email" label="Email" :value="$model->email"
                                    placeholder="Enter Email" autocomplete="off" mandatory="true" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <x-Inputs.group-text-field name="mobile" label="Mobile" class="validate-mobile"
                                    :value="$model->mobile" placeholder="Enter Mobile"
                                    secondaryText='<span id="mobile_country_code" class="input-group-text">+91</span>'
                                    autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    @if ($is_add_form)
                        <div class="row">
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group mb-3">
                                    <x-Inputs.text-field type="password" name="password" label="Password"
                                        placeholder="Enter Password" mandatory="true" />
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group mb-3">
                                    <x-Inputs.text-field type="password" name="password_confirm" label="Confirm Password"
                                        placeholder="Enter Confirm Password" mandatory="true" />
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <?php
                                $value = implode(',', $model->userRole->pluck('role_id')->toArray());
                                ?>
                                <x-Inputs.drop-down name="roles[]" label="Roles" :list="$role_list" :value="$value"
                                    class="select2" multiple="multiple" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-3">
                                <div class="row" style="margin-top:30px;">
                                    <div class="col-sm-8">
                                        <input type="hidden" id="profile_image" name="profile_image"
                                            value="{{ old('profile_image') }}" />
                                        <span id="modal_crop_opener" class="btn btn-secondary">Choose Profile
                                            Photo</span>
                                    </div>
                                    <div class="col-sm-4">
                                        @if ($model->profile_image_url)
                                            <a id="profile_photo_block" class="fancybox"
                                                data-fancybox="group-{{ $model->id }}"
                                                href="{{ $model->profile_image_url }}">
                                                <img class="img-thumbnail rounded-circle avatar-xl"
                                                    src="{{ $model->profile_image_url }}" />
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group mb-3">
                                <table class="table table-sm table-striped table-bordered order-column template-table"
                                    data-sr-table-t emplate-min-row="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width : 80px;">
                                                <span class="sr-table-template-add">
                                                    <i class="fas fa-plus-circle text-success icon"></i>
                                                </span>
                                            </th>
                                            <th style="width : 30%;">Allow IP For Auto Login</th>
                                            <th style="width : 50%;">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="sr-table-template-row hidden">
                                            <td>
                                                <div class="block">
                                                    <div class="left-block">

                                                    </div>
                                                    <div class="right-block">
                                                        <span class="sr-table-template-delete">
                                                            <i class=" fas fa-times-circle text-danger icon"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <x-inputs.text-field type="hidden" name="allow_ip[(sr-id)][id]"
                                                    label="" />
                                            </td>
                                            <td>
                                                <x-inputs.text-field name="allow_ip[(sr-id)][ip]" label=""
                                                    placeholder="Enter IP" />
                                            </td>
                                            <td>
                                                <x-inputs.text-field name="allow_ip[(sr-id)][description]" label=""
                                                    placeholder="Enter Description" />
                                            </td>
                                        </tr>
                                        @php
                                            $allow_ip = old('allow_ip', $allow_ip);
                                            unset($allow_ip['(sr-id)']);
                                        @endphp
                                        @if (isset($allow_ip))
                                            @foreach ($allow_ip as $k => $allow_arr)
                                                <tr class="" sr-id="{{ $allow_arr['id'] }}"
                                                    sr-counter="{{ $k }}">
                                                    <td>
                                                        <div class="block">
                                                            <div class="left-block">
                                                            </div>
                                                            <div class="right-block">
                                                                <span class="sr-table-template-delete">
                                                                    <i class=" fas fa-times-circle text-danger icon"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="allow_ip[{{ $k }}][id]"
                                                            value="{{ $allow_arr['id'] }}" />
                                                    </td>
                                                    <td>
                                                        <?php $value = $allow_arr['ip']; ?>
                                                        <x-inputs.text-field name="allow_ip[{{ $k }}][ip]"
                                                            label="" :value="$value" placeholder="Enter IP" />
                                                    </td>
                                                    <td>
                                                        <?php $value = $allow_arr['description']; ?>
                                                        <x-inputs.text-field
                                                            name="allow_ip[{{ $k }}][description]"
                                                            label="" placeholder="Enter Description"
                                                            :value="$value" />
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="text-center" style="width : 80px;">
                                                <span class="sr-table-template-add">
                                                    <i class="fas fa-plus-circle text-success icon"></i>
                                                </span>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group mb-3">
                                <x-Inputs.checkbox name="dont_send_email" label="Don't Send Email" :value="$model->dont_send_email" />
                                <x-Inputs.checkbox name="is_active" label="Active" :value="$model->is_active" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-Backend.form-common-footer-buttons />
        </div>
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
            var my_country = JSON.parse('{!! json_encode($my_country) !!}');
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

                    $.post(url, data, function(responseText) {
                        console.log(responseText);
                        ajaxHandleResponse(url, responseText, function(response) {
                            $("#profile_image").val(response['file']);
                            $("#inline-loader").hide();
                            $("#modal-crop").modal("hide");
                        });
                    });
                });
            });



            $.events.execAtLast(function() {
                $(".template-table").srTableTemplate({
                    brace_type: "(",
                });

                $("#type").change(function() {
                    var v = $(this).val();

                    $(".type-block").hide();
                    $(".type-block").find("input,select").attr("disabled", true);

                    var selector = ".type-block-" + v;

                    if ($(selector).length > 0) {
                        $(selector).show();
                        $(selector).find("input,select").removeAttr("disabled");
                    }

                    $("#name").removeClass("validate-alpha-numeric");

                    if (v == '{{ User::TYPE_INTERNAL }}') {
                        $("#name").addClass("validate-alpha-numeric");
                        $("#country_id").val(my_country['id']).trigger("change");
                    }
                }).trigger("change");

                $("#country_id").change(function() {
                    var v = $(this).val();

                    var data_value = $(this).attr("data-value");

                    if (data_value && !v) {
                        v = data_value;
                    }

                    if (v) {
                        $("#mobile_country_code").html("");
                        ajaxGetJson("/country/ajax_get/" + v, function(response) {
                            var data = response['data'];

                            $("#mobile_country_code").html("+" + data['phonecode']);
                        });
                    }
                });

                $(".cascade-drop-down").trigger("change", {
                    pageLoad: true
                });
            });
        });
    </script>
@endsection
