@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'Create', 'url' => route($routePrefix . '.create')]];
    
    ?>

    @include($partial_path . '.page_header')

    <div class="card">
        <div class="card-body">
            <form method="GET" class="summary_search" action="{{ route($routePrefix . '.index') }}">
                <input type="hidden" name="is_sort_clear" value="1" />
                <div class="row mb-4">
                    <div class="col-md-3">
                        <x-Inputs.text-field name="name" label="Name" :value="$search['name']" />
                    </div>
                    <div class="col-md-3">
                        <x-Inputs.text-field name="short_name" label="Short Name" :value="$search['short_name']" />
                    </div>
                    <div class="col-md-3">
                        <x-Inputs.text-field name="phonecode" label="Phonecode" :value="$search['phonecode']" />
                    </div>
                </div>
                <x-Backend.summary-search-form-footer :selectedPaginationLimit="$pagination_limit" />
            </form>
        </div>
    </div>

    <div id="index_table">
        @include($viewPrefix . '.index_table')
    </div>
@endsection
