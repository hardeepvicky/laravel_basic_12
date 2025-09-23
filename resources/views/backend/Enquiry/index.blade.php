@extends($layout)

@section('content')
    <?php
    $page_header_links = [];
    
    ?>

    @include($partial_path . '.page_header')

    <div class="card">
        <div class="card-body">
            <form method="GET" class="summary_search" action="{{ route($routePrefix . '.index') }}">
                <input type="hidden" name="is_sort_clear" value="1" />
                <div class="row mb-4">
                    <div class="col-sm-6 col-md-3">
                        <x-Inputs.text-field name="name" label="Name" :value="$search['name']" autocomplete="off" />
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <x-Inputs.text-field name="mobile" label="Mobile" :value="$search['mobile']" autocomplete="off" />
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <x-Inputs.text-field name="email" label="Email" :value="$search['email']" autocomplete="off" />
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <x-Inputs.drop-down name="country_id" label="Country" :list="$country_list" :value="$search['country_id']"
                            class="select2 cascade-drop-down" data-sr-cascade-target="#state_id"
                            data-sr-cascade-url="/enquiry/ajax_get_state_list/{v}" />
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <?php $list = []; ?>
                        <x-Inputs.drop-down id="state_id" name="state_id" label="State" :list="$list"
                            :data-value="$search['state_id']" class="select2 cascade-drop-down" data-sr-cascade-target="#city_id"
                            data-sr-cascade-url="/enquiry/ajax_get_city_list/{v}" />
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <?php $list = []; ?>
                        <x-Inputs.drop-down id="city_id" name="city_id" label="City" :list="$list" class="select2"
                            :data-value="$search['city_id']" />
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <x-Inputs.drop-down name="source_id" label="Source" :list="$source_list" :value="$search['source_id']"
                            class="select2" />
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <x-Inputs.drop-down name="type_id" label="Type" :list="$type_list" :value="$search['status_id']"
                            class="select2" />
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <x-Inputs.drop-down name="status_id" label="Status" :list="$status_list" :value="$search['status_id']"
                            class="select2" />
                    </div>


                    <div class="col-sm-6 col-md-3">
                        <x-Inputs.drop-down name="category_id" label="Product Category" :list="$category_list"
                            :value="$search['category_id']" class="select2" />
                    </div>
                </div>
                <x-Backend.summary-search-form-footer :selectedPaginationLimit="$pagination_limit" />
            </form>
        </div>
    </div>

    <div id="index_table">
        @include($viewPrefix . '.index_table')
    </div>


    <script>
        $(document).ready(function() {
            $.events.execAtLast(function() {
                $(".cascade-drop-down").trigger("sr-cascade.change", {
                    pageLoad: true
                });
            });
        })
    </script>
@endsection
