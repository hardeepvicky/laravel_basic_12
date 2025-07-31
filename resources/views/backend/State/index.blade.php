@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'Create', 'url' => route($routePrefix . '.create')]];
    
    ?>

    <div class="row">
        <div class="col-xxxl-10">
            @include($partial_path . '.page_header')

            <div class="card">
                <div class="card-body">
                    <form method="GET" class="summary_search" action="{{ route($routePrefix . '.index') }}">
                        <input type="hidden" name="is_sort_clear" value="1" />
                        <div class="row mb-4">
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                <x-Inputs.text-field name="name" label="Name" :value="$search['name']" />
                            </div>
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                <x-Inputs.text-field name="short_name" label="Short Name" :value="$search['short_name']" />
                            </div>
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                <x-Inputs.drop-down name="country_id" label="Country" :value="$search['country_id']" :list="$country_list"
                                    class="select2" />
                            </div>
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                <x-Inputs.drop-down name="is_active" label="Active" :value="$search['is_active']" :list="$yes_no_list"
                                    class="select2" />
                            </div>
                        </div>
                        <x-Backend.summary-search-form-footer :selectedPaginationLimit="$pagination_limit" />
                    </form>
                </div>
            </div>

            <div id="index_table">
                @include($viewPrefix . '.index_table')
            </div>
        </div>
    </div>
@endsection
