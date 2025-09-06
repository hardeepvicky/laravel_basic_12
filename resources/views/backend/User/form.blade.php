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
                <div class="col-md-12 col-xxl-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'type';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$type_list"
                                    :value="$model->{$field}" class="select2" mandatory="true" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3 ">
                                <?php
                                $field = 'sub_type';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$sub_type_list"
                                    :value="$model->{$field}" class="select2 sub_type" mandatory="true" />
                            </div>
                        </div>
                        <div class="col-md-6 type-block type-block-{{ User::TYPE_INTERNAL }}">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'user_category_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$user_category_list" :value="$model->{$field}" class="select2" mandatory="true" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3 type-block type-block-{{ User::TYPE_EXTERNAL }}">
                        <?php
                        $field = 'country_id';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$country_list"
                            :value="$model->{$field}" class="select2" mandatory="true" />
                    </div>

                    <div class="form-group mb-3 type-block type-block-{{ User::TYPE_INTERNAL }}">
                        <?php
                        $field = 'branch_id';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$branch_list"
                            :value="$model->{$field}" class="select2" mandatory="true" />
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                                    :value="$model->{$field}" mandatory="true" placeholder="Enter {{ $label }}" />
                                <div class="help-block">This will use as login id</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'email';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field type="email" :id="$field" :name="$field" :label="$label"
                                    :value="$model->{$field}" mandatory="true" placeholder="Enter {{ $label }}" />
                                <div class="help-block">This will use as login id</div>
                            </div>
                        </div>
                    </div>

                    @if ($is_add_form)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <?php
                                    $field = 'password';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.text-field type="password" :id="$field" :name="$field"
                                        :label="$label" mandatory="true" placeholder="Enter {{ $label }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <?php
                                    $field = 'password_confirm';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.text-field type="password" :id="$field" :name="$field"
                                        :label="$label" mandatory="true" placeholder="Enter {{ $label }}" />
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'person_name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                                    :value="$model->{$field}" mandatory="true" placeholder="Enter {{ $label }}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'person_last_name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                                    :value="$model->{$field}" placeholder="Enter {{ $label }}" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'mobile';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.group-text-field :id="$field" :name="$field" :label="$label"
                                    :value="$model->{$field}" class="validate-mobile" placeholder="Enter {{ $label }}"
                                    mandatory="true"
                                    secondaryText='<span id="mobile_country_code" class="input-group-text">+91</span>' />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'whatsapp_mobile';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.group-text-field :id="$field" :name="$field" :label="$label"
                                    class="validate-mobile" :value="$model->{$field}" placeholder="Enter {{ $label }}"
                                    secondaryText='<span class="input-group-text text-center"><span id="same_as_mobile" class="btn btn-secondary btn-sm">Same As Mobile</span></span>'
                                    mandatory="true" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'voip_mobile';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.group-text-field :id="$field" :name="$field" :label="$label"
                                    :value="$model->{$field}" 
                                    class="validate-mobile" 
                                    placeholder="Enter {{ $label }}"
                                    secondaryText='<span id="mobile_country_code" class="input-group-text">+91</span>' />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <?php
                                $field = 'office_landline_no';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                                    :value="$model->{$field}" class="validate-mobile" placeholder="Enter {{ $label }}" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3 type-block type-block-{{ User::TYPE_INTERNAL }}">
                                <?php
                                $field = 'dept_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$department_list" :value="$model->{$field}" class="select2 cascade-drop-down"
                                    data-sr-cascade-target="#sub_dept_id"
                                    data-sr-cascade-url="/sub-department/ajax_get_list/{v}" mandatory="true" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3 type-block type-block-{{ User::TYPE_INTERNAL }}">
                                <?php
                                $field = 'sub_dept_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$empty_list" :value="$model->{$field}" class="select2" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3 type-block type-block-{{ User::TYPE_INTERNAL }}">
                        <?php
                        $field = 'desg_id';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$designation_list"
                            :value="$model->{$field}" class="select2" mandatory="true" />
                    </div>
                    <div class="form-group mb-3">
                        <?php
                        $value = implode(',', $model->userRole->pluck('role_id')->toArray());
                        ?>
                        <x-Inputs.drop-down name="roles[]" label="Roles" :list="$role_list" :value="$value"
                            class="select2" multiple="multiple" mandatory="true" />
                    </div>

                    <div class="form-group mb-3 type-block type-block-{{ User::TYPE_INTERNAL }}">
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
                                    $allow_ip = old('allow_ip', $allow_ip ?? []);
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
                                                <x-inputs.text-field name="allow_ip[{{ $k }}][description]"
                                                    label="" placeholder="Enter Description" :value="$value" />
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

                    <div class="row">
                        <div class="col-md-6">
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
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <x-Inputs.checkbox name="dont_send_email" label="Don't Send Email" :value="$model->dont_send_email" />
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

                    if (v == '{{ User::TYPE_INTERNAL }}') {
                        $(".sub_type").val('<?= User::SUB_TYPE_TEAM_MEMBER ?>').trigger("change");
                    }

                }).trigger("change", {
                    pageLoad: true
                });

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

                $("#dept_id").trigger("change", {
                    pageLoad: true
                });

                $("#same_as_mobile").click(function() {
                    $("#whatsapp_mobile").val($("#mobile").val());
                });
            });
        });
    </script>
@endsection
