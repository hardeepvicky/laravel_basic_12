@extends($layout)

@section('content')
    <?php
    $page_header_links = [];
    ?>

    @include($partial_path . '.page_header')


    <div class="card">
        <div class="card-body">
            <form method="GET">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <x-inputs.text-field name="from_email" label="From Email" :value="$search['from_email']" />
                    </div>
                    <div class="col-md-4">
                        <x-inputs.text-field name="to_email" label="To Email" :value="$search['to_email']" />
                    </div>
                    <div class="col-md-4">
                        <x-inputs.text-field id="from_date" name="from_date" class="form-control date-picker"
                            label="From Created Date" :value="$search['from_date']" autocomplete="off" data-date-end="input#to_date" />
                    </div>
                    <div class="col-md-4">
                        <x-inputs.text-field id="to_date" name="to_date" class="form-control date-picker"
                            label="To Created Date" :value="$search['to_date']" autocomplete="off"
                            data-date-start="input#from_date" />
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-sm-6 col-md-4">
                        <div>
                            <button type="submit" class="btn btn-primary w-md">Search</button>
                            <span class="btn btn-light w-md clear_form_search_conditions">Clear</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="index_table">
        @include($viewPrefix . '.email_logs_index_table')
    </div>
@endsection
