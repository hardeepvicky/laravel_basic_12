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
                            $field = 'product_serial_no';
                            $label = 'Serial No.';
                            ?>
                            <x-Inputs.text-field :id="$field" :name="$field" :label="$label"
                                placeholder="Enter {{ $label }}" layout="horizontal" horizontalSecondColumnLength="3"
                                mandatory="true" />

                            <div class="row mb-3">
                                <div id="result_status" class="alert alert-info">Result</div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header border-l-secondary card-no-border">
                                            <h3>Products</h3>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm summary-table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Sr. No.</th>
                                                        <th>Category</th>
                                                        <th>Model No.</th>
                                                        <th>Qty</th>
                                                        <th>Sent Qty</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($model->products as $i => $dispatchOrderProduct)
                                                        <tr id="dop-{{ $dispatchOrderProduct->id }}">
                                                            <td>{{ $i + 1 }}</td>
                                                            <td>{{ $dispatchOrderProduct->product?->category->getDisplayName() }}
                                                            </td>
                                                            <td>{{ $dispatchOrderProduct->product?->getDisplayName() }}</td>
                                                            <td>{{ $dispatchOrderProduct->qty }}</td>
                                                            <td class="sent_qty">{{ $dispatchOrderProduct->dispatchOrderProductSerial->count() }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header border-l-secondary card-no-border">
                                            <h3>Product Serials</h3>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm summary-table table-bordered" id="product_serials">
                                                <thead>
                                                    <tr>
                                                        <th>Sr. No.</th>
                                                        <th>Serial No.</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $i = 0; ?>
                                                    @foreach ($model->products as $dispatchOrderProduct)
                                                    @foreach ($dispatchOrderProduct->dispatchOrderProductSerial as $dispatchOrderProductSerial)
                                                        <?php $i++; ?>
                                                        <tr id="serial-{{ $dispatchOrderProductSerial->productSerial->serial_no }}">
                                                            <td>{{ $i }}</td>
                                                            <td>{{ $dispatchOrderProductSerial->productSerial->serial_no }}</td>   
                                                            <td>
                                                                <a class="ajax_delete_product_serial" href="{{ route($routePrefix . ".ajax_delete_product_serial", [$dispatchOrderProductSerial->id]) }}">
                                                                    <span class="btn btn-outline-danger summary-action-button">
                                                                        <i class="<?= ICON_SUMMARY_DELETE ?> label-icon"></i>
                                                                    </span>
                                                                </a>
                                                            </td>                                                         
                                                        </tr>
                                                    @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {

            function ajax_in() {
                var product_serial_no = $("#product_serial_no").val();
                product_serial_no = product_serial_no ? product_serial_no.trim() : "";

                if (!product_serial_no) {
                    $("#product_serial_no").focus();
                    return;
                }

                var url = '{{ route($routePrefix . ".send", [$model->id]) }}';

                $.loader.show();

                $("#result_status").removeClass("alert-info alert-success alert-danger");

                $("#product_serial_no").val("").focus();

                $.post(url, {
                    product_serial_no: product_serial_no
                }, function(response) {

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

                        var len = $("#product_serials").find("tbody tr").length;

                        var html = '<tr id="serial-' + responseJson['product_serial_no'] + '">';
                            html += "<td>";
                                html += (len + 1);
                            html += "</td>";
                            html += "<td>";
                                html += responseJson['product_serial_no'];
                            html += "</td>";
                            html += "<td>";                                
                                html += '<a class="ajax_delete_product_serial" href="' + responseJson['delete_url'] + '">';
                                    html += '<span class="btn btn-outline-danger summary-action-button">';
                                        html += '<i class="<?= ICON_SUMMARY_DELETE ?> label-icon"></i>';
                                    html += '</span>';
                                html += '</a>';
                            html += "</td>";
                        html += "</tr>";

                        $("#product_serials").find("tbody").append(html);

                        if (typeof responseJson['dispatchOrderProduct'] == "object")
                        {
                            var selector = "tr#dop-" + responseJson['dispatchOrderProduct']['id'];

                            $(selector).find(".sent_qty").html(responseJson['dispatchOrderProduct']['sent_qty']);
                        }

                    } else if (typeof responseJson["msg"] != "undefined") {

                        const synth = window.speechSynthesis;
                        const utterance = new SpeechSynthesisUtterance(responseJson["msg"]);
                        synth.speak(utterance);

                        $("#result_status").addClass("alert-danger").html(responseJson["msg"]);
                    } else {
                        $("#result_status").addClass("alert-danger").html("Unknown Error");
                    }
                })
            }

            $("#product_serial_no").keyup(function(e) {
                if (e.key === "Enter") {
                    ajax_in();
                }
            });

            $(document).on("click", "a.ajax_delete_product_serial", function(){
                var _tr = $(this).closest("tr");

                var url = $(this).attr("href");

                ajaxGetJson(url, function(response){
                    if (typeof response['dispatchOrderProduct'] == "object")
                    {
                        var selector = "tr#dop-" + response['dispatchOrderProduct']['id'];

                        $(selector).find(".sent_qty").html(response['dispatchOrderProduct']['sent_qty']);
                    }

                    _tr.remove();
                });

                return false;
            });
        });
    </script>
@endsection
