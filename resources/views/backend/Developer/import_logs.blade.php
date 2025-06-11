@extends($layout)

@section('content')

<?php
    $page_header_links = [];
?>

@include($common_elements_path . ".page_header")


<div class="card">
    <div class="card-body">
        <form method="GET" class="summary_search" action="{{ route($routePrefix . '.import_logs') }}">
            <div class="row mb-4">
                <div class="col-md-3">
                    <?php
                        $type_list = config('constant.import_type');
                    ?>
                    <x-inputs.drop-down name="type" label="Type" :value="$type" :list="$type_list" class="form-control select2"/>
                </div>
                <div class="col-md-3">
                    <?php
                        $yes_no = config('constant.yes_no');
                    ?>
                    <x-inputs.drop-down name="is_fully_run" label="Fully Run" :value="$is_fully_run" :list="$yes_no" class="form-control select2"/>
                </div>  
                <div class="col-md-3">
                    <?php
                        $yes_no = config('constant.yes_no');
                    ?>
                    <x-inputs.drop-down name="have_error" label="Have Error" :value="$have_error" :list="$yes_no" class="form-control select2"/>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-3">
                    <x-inputs.text-field id="from_date" name="from_date" class="form-control date-picker" label="From Created Date" :value="$from_date" autocomplete="off" data-date-end="input#to_date" />
                </div>
                <div class="col-md-3">
                    <x-inputs.text-field id="to_date" name="to_date" class="form-control date-picker" label="To Created Date" :value="$to_date" autocomplete="off" data-date-start="input#from_date" />
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
    @include($viewPrefix . ".import_logs_index_table")
</div>

@endsection
