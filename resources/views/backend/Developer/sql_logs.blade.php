@extends($layout)

@section('content')
<?php
$page_header_links = [];
?>

@include($partial_path . '.page_header')


<div class="card">
    <div class="card-body new-btn-showcase">
        <form method="GET" class="summary_search">
            <input type="hidden" name="is_sort_clear" value="1" />
            <div class="row mb-4">
                <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                    <x-Inputs.text-field name="route_name" label="Route Name" :value="$search['route_name']" />
                </div>
                <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                    <x-Inputs.text-field name="request_method" label="Request Method" :value="$search['request_method']" />
                </div>
                <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                    <x-Inputs.text-field id="from_date" name="from_date" label="From Created Date"
                        :value="$search['from_date']" class="form-control date-picker" data-date-end="input#to_date"
                        autocomplete="off" />
                </div>
                <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                    <x-Inputs.text-field id="to_date" name="to_date" label="To Created Date" :value="$search['to_date']"
                        class="form-control date-picker" data-date-start="input#from_date" autocomplete="off" />
                </div>
                <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                    <x-Inputs.drop-down name="have_dml_query" label="Have DML Query" :list="$yes_no_list"
                        :value="$search['have_dml_query']" class="select2" />
                </div>
                <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                    <x-Inputs.drop-down name="have_heavy_query" label="Have Heavy Query" :list="$yes_no_list"
                        :value="$search['have_heavy_query']" class="select2" />
                </div>
            </div>
            <x-Backend.summary-search-form-footer :selectedPaginationLimit="$pagination_limit" />
        </form>
    </div>
</div>

<div id="index_table">
    @include($viewPrefix . '.sql_logs_index_table')
</div>
@endsection