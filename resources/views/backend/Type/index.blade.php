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
                    <div class="col-sm-6 col-md-3 col-xl-2 mb-2">
                        <?php
                        $field = 'pagination_limit';
                        ?>
                        <x-Inputs.drop-down :name="$field" label="Limit" :list="$paginationList" :value="$pagination_limit" :pleaseSelect="$false"
                            class="pagination_limit" />
                    </div>
                    <div class="col-md-6 col-lg-4 col-xxxl-3 mb-2">
                        <x-Inputs.drop-down name="type" label="Type" :list="$type_list" :value="$search['type']"
                            class="select2" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <x-Inputs.text-field name="name" label="Name" :value="$search['name']" autocomplete="off" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <x-Inputs.text-field name="short_name" label="Short Name" :value="$search['short_name']" autocomplete="off" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <x-Inputs.text-field name="code" label="Code" :value="$search['code']" autocomplete="off" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <x-Inputs.drop-down name="is_active" label="Active" :value="$search['is_active']" :list="$yes_no_list"
                            class="select2" />
                    </div>
                </div>
                <x-Backend.summary-search-form-footer :showPaginationDropDown="$false" />
            </form>
        </div>
    </div>

    <div id="index_table">
        @include($viewPrefix . '.index_table')
    </div>

@endsection
