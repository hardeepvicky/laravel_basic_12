@extends($layout)

@section('content')
    <?php
    use App\Helpers\DateUtility;
    $page_header_links = [['title' => 'List', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <x-Backend.form-errors />

    <div class="row">
        <div class="col-xxxl-12">
            <div class="card">
                <div class="card-header card-no-border pb-0">
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 col-xxl-6">
                            <?php
                            $field = 'branch_id';
                            $label = get_value_from_list($field_labels, $field, $field);
                            ?>
                            <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$branch_list"
                                class="select2 " layout="horizontal" mandatory="true" />


                            <?php
                                $field = 'product_serial_no';
                                $label = "Serial No.";
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field"
                                :label="$label"
                                placeholder="Enter {{ $label }}"
                                layout="horizontal"
                                horizontalSecondColumnLength="3"
                                mandatory="true"
                            />

                            <?php
                                $field = 'bin_code';
                                $label = "Location Code";
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field"
                                :label="$label"
                                placeholder="Enter {{ $label }}"
                                layout="horizontal"
                                horizontalSecondColumnLength="3"
                            />

                            <div class="row mb-3">
                                <div id="result_status" class="alert alert-info">Result</div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    $(function() {

        function ajax_in()
        {
            var branch_id = $("#branch_id").val();
            branch_id = branch_id ? branch_id.trim() : "";

            var bin_code = $("#bin_code").val();
            bin_code = bin_code ? bin_code.trim() : "";

            var product_serial_no = $("#product_serial_no").val();
            product_serial_no = product_serial_no ? product_serial_no.trim() : "";

            if (!branch_id)
            {
                $("#branch_id").focus();
                return;
            }

            if (!product_serial_no)
            {
                $("#product_serial_no").focus();
                return;
            }

            var url = '{{ route($routePrefix . ".in") }}';

            $.loader.show();

            $("#result_status").removeClass("alert-info alert-success alert-danger");

            $("#product_serial_no").val("").focus();

            $.post(url, {
                branch_id : branch_id,
                bin_code : bin_code,
                product_serial_no : product_serial_no
            }, function(response){

                $.loader.hide();

                var responseJson = {};
                if (typeof response == "object") {
                    responseJson = response;
                } else {
                    try {
                        if (typeof response == "string") {
                            response = response.trim();

                            if (response.length == 0) {
                                $.events.onAjaxError("JSON Parse Error", "Empty Response", {
                                    url: url,
                                });

                                return false;
                            }

                            var responseJson = JSON.parse(response);
                        }
                    } catch (e) {
                        $.loader.hide();
                        $.events.onAjaxError("JSON Parse Error", response, {
                            url: url,
                        });
                        return false;
                    }
                }

                if (typeof responseJson["status"] == "undefined") {
                    $.events.onAjaxError("Missing", "Response JSON Should have status", {
                        url: url,
                    });
                    return;
                }

                console.log(responseJson);

                if (responseJson["status"] == "1" || responseJson["status"] == true) {
                    
                    $("#result_status").addClass("alert-success").html("Success, Last Serial No. : " + product_serial_no);

                } else if (typeof responseJson["msg"] != "undefined") {
                    
                    $("#result_status").addClass("alert-danger").html(responseJson["msg"]);
                } else {
                    $("#result_status").addClass("alert-danger").html("Unknown Error");
                }
            })
        }

        $("#product_serial_no").keyup(function(e){
            if (e.key === "Enter") {
                ajax_in();
            }
        });

        $("#bin_code").keyup(function(e){
            if (e.key === "Enter") {
                ajax_in();
            }
        });
    });
</script>@endsection
