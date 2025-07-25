@extends($layout)

@section('content')
    <?php
    $page_header_links = [];
    ?>

    @include($partial_path . '.page_header')


    <div class="card">
        <div class="card-body">
            <form method="GET" class="summary_search">
                <input type="hidden" name="is_sort_clear" value="1" />
                <div class="row mb-4">
                    <div class="col-md-4 col-xl-3 col-xxl-2">
                        <x-inputs.text-field name="name" label="Name" :value="$search['name']" />
                    </div>
                    <div class="col-md-4 col-xl-3 col-xxl-2">
                        <x-inputs.text-field name="mobile_no" label="Mobile No." :value="$search['mobile_no']" />
                    </div>
                    <div class="col-md-4 col-xl-3 col-xxl-2">
                        <x-inputs.text-field id="from_date" name="from_date" class="form-control date-picker"
                            label="From Created Date" :value="$search['from_date']" autocomplete="off" data-date-end="input#to_date" />
                    </div>
                    <div class="col-md-4 col-xl-3 col-xxl-2">
                        <x-inputs.text-field id="to_date" name="to_date" class="form-control date-picker"
                            label="To Created Date" :value="$search['to_date']" autocomplete="off"
                            data-date-start="input#from_date" />
                    </div>
                </div>
                <x-Backend.summary-search-form-footer :selectedPaginationLimit="$pagination_limit" />
            </form>
        </div>
    </div>

    <div id="index_table">
        @include($viewPrefix . '.whatsapp_logs_index_table')
    </div>
@endsection
