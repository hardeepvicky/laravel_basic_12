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
                <div class="row mb-2">
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <?php 
                            $field = "type";
                        ?>
                        <x-Inputs.text-field :name="$field" label="Type" :value="$search[$field]" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <?php 
                            $field = "name";
                        ?>
                        <x-Inputs.text-field :name="$field" label="Name" :value="$search[$field]" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <?php 
                            $field = "short_name";
                        ?>
                        <x-Inputs.text-field :name="$field" label="Short Name" :value="$search[$field]" />
                    </div>      
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <?php 
                            $field = "code";
                        ?>
                        <x-Inputs.text-field :name="$field" label="Code" :value="$search[$field]" />
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
