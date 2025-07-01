@extends($layout)

@section('content')
    <div class="py-5 text-center">
        <h2>Enquiry form</h2>
    </div>

    <x-Frontend.session-flash />

    <x-Frontend.form-errors />

    <form action="{{ route('enquiry.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation mb-4"
        novalidate>
        {!! csrf_field() !!}
        {{ method_field('post') }}
        <div class="row g-3">
            <div class="col-md-6">
                <x-Inputs.drop-down name="country_id" label="Country" :list="$country_list" :value="$record['country_id']"
                    class="select2 cascade-drop-down" mandatory="true" data-sr-cascade-target="#state_id"
                    data-sr-cascade-url="/enquiry/ajax_get_state_list/{v}" />
                <div class="invalid-feedback">
                    Please select a valid country.
                </div>
            </div>
            <div class="col-md-6">
                <?php $list = []; ?>
                <x-Inputs.drop-down id="state_id" name="state_id" label="State" :list="$list" :data-value="$record['state_id']"
                    mandatory="true" class="select2 cascade-drop-down" data-sr-cascade-target="#city_id"
                    data-sr-cascade-url="/enquiry/ajax_get_city_list/{v}" />
                <div class="invalid-feedback">
                    Please select a valid state.
                </div>
            </div>
            <div class="col-md-6">
                <?php $list = []; ?>
                <x-Inputs.drop-down id="city_id" name="city_id" label="City" :list="$list" class="select2"
                    :data-value="$record['city_id']" mandatory="true" />
                <div class="invalid-feedback">
                    Please select a valid city.
                </div>
            </div>

            <div class="col-md-6">
                <x-Inputs.text-field id="mobile" name="mobile" label="Mobile" placeholder="Enter Mobile"
                    mandatory="true" class="form-control validate-mobile" />
                <div class="invalid-feedback">
                    Valid Mobile is required.
                </div>
            </div>

            <div class="col-md-6">
                <x-Inputs.text-field name="name" label="Name" placeholder="Enter Name" mandatory="true" />
                <div class="invalid-feedback">
                    Valid Name is required.
                </div>
            </div>

            <div class="col-md-6">
                <x-Inputs.text-field type="email" name="email" label="Email" placeholder="Enter Email" />
                <div class="invalid-feedback">
                    Valid Email is required.
                </div>
            </div>

            <div class="col-md-6">
                <x-Inputs.drop-down name="source_id" label="Source" :list="$source_list" :value="$record['source_id']" mandatory="true"
                    class="select2" />
                <div class="invalid-feedback">
                    Please select a valid type.
                </div>
            </div>

            <div class="col-md-6">
                <x-Inputs.drop-down name="type_id" label="Type" :list="$type_list" :value="$record['status_id']" mandatory="true"
                    class="select2" />
                <div class="invalid-feedback">
                    Please select a valid type.
                </div>
            </div>

            <div class="col-md-6">
                <x-Inputs.drop-down name="status_id" label="Status" :list="$status_list" :value="$record['status_id']" mandatory="true"
                    class="select2" />
                <div class="invalid-feedback">
                    Please select a valid status.
                </div>
            </div>


            <div class="col-md-6">
                <x-Inputs.drop-down name="category_id" label="Product Category" :list="$category_list" class="select2" />
                <div class="invalid-feedback">
                    Please select a valid Product Category.
                </div>
            </div>

            <div class="col-sm-12">
                <x-Inputs.text-field name="address" label="Address" placeholder="Enter Address" />
                <div class="invalid-feedback">
                    Enter Valid Address.
                </div>
            </div>

            <div class="col-sm-12">
                <x-Inputs.text-area name="message" label="Message" placeholder="Enter Message" />
                <div class="invalid-feedback">
                    Enter Valid Message.
                </div>
            </div>
            <div class="col-sm-12">
                <x-Inputs.file-field class="image" name="file" label="Picture" />
                <div class="invalid-feedback">
                    Please choose image less than 10mb and only png,jpg,jpeg,gif,svg
                </div>
            </div>
            <div class="col-sm-12 mb-4">
                <button class="btn btn-primary w-100">Submit</button>
            </div>

        </div>
    </form>

    <script>
        var is_valid_file = true;
        $(document).ready(function() {
            $.events.execAtLast(function() {
                $(".cascade-drop-down").trigger("change", {
                    pageLoad: true
                });
            });

            $('input.image').fileValidator({
                onValidation: function(files) {
                    $(this).parent().find(".invalid-feedback").hide();
                },
                onInvalid: function(type, file) {
                    is_valid_file = false;
                    $(this).parent().find(".invalid-feedback").show();
                },
                maxSize: '10m',
                type: 'image'
            });

            $("form").submit(function() {
                if (!is_valid_file) {
                    $.events.onUserError("Please Select Valid Image");
                    return false;
                }
            });
        })
    </script>
@endsection
