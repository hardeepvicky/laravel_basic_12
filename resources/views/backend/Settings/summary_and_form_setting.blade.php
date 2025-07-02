@extends($layout)

@section('content')

@include($partial_path . '.page_header')

<div class="card">
    <div class="card-body">
        <div style="width : 500px; margin: auto;">
            <x-Inputs.drop-down id="type" name="type" label="Type" :value="$search['type']" :list="$model_list"
                class="select2" />
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <ul class="simple-wrapper nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link text-primary" data-bs-toggle="tab" href="#tab_field_labels"
                    role="tab" aria-controls="home" aria-selected="true">Home</a></li>
            <li class="nav-item">
                <a class="nav-link text-primary" data-bs-toggle="tab" href="#tab_other" role="tab"
                    aria-controls="profile" aria-selected="false">Other</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade" id="tab_field_labels" role="tabpanel" aria-labelledby="tab_field_labels">
                <p class="pt-3">Tabs have long been used to show alternative views of the same group of information tabs
                    in software. Known as<em class="text-danger"> “module tabs”</em>, these are still used today in web
                    sites. For instance, airline companies such as Ryanair, easyJet and AirMalta use module tabs to
                    enable the user to switch between bookings for flights, hotels and car hire.</p>
            </div>
            <div class="tab-pane fade active show mt-4" id="tab_other" role="tabpanel" aria-labelledby="tab_other">
                <div class="flex-space flex-wrap align-items-center">
                    <img class="tab-image" src="../assets/images/avtar/3.jpg" alt="profile">
                    <ul class="d-flex flex-column gap-1">
                        <li><strong>Visit Us:</strong> 2600 Hollywood Blvd, Florida, United States-33020</li>
                        <li><strong>Mail Us:</strong> contact@us@gmail.com</li>
                        <li><strong>Contact Number:</strong> (954) 357-7760</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>



    <script>
        $(function() {
            $.events.execAtLast(function() {
                $("#type").change(function() {
                    var v = $(this).val();

                    if (v) {
                        confirmDialog("This Will loose your data", function() {
                            window.location.href = "/setting/summary_and_form_setting/" + v;
                        })
                    }
                })
            });
        })
    </script>

    @endsection