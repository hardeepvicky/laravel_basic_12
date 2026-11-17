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
                    <div class="col-md-6 col-lg-4 col-xxxl-3 mb-2">
                        <?php 
                            $field = "parent_id";
                            $tree_list = [0 => "Root"] + $tree_list;
                        ?>
                        <x-Inputs.drop-down :name="$field" label="Parent" :value="$search[$field]" :list="$tree_list"
                            class="select2" />
                        <?php 
                            $field = "is_find_nested_child";
                        ?>
                        <x-Inputs.checkbox :name="$field" label="Find Nested Child" :value="$search[$field]" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <?php 
                            $field = "name";
                        ?>
                        <x-Inputs.text-field :name="$field" label="Name" :value="$search[$field]" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <?php 
                            $field = "route_name";
                        ?>
                        <x-Inputs.text-field :name="$field" label="Route Name" :value="$search[$field]" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <?php 
                            $field = "is_link";
                        ?>
                        <x-Inputs.drop-down :name="$field" label="Link" :value="$search[$field]" :list="$yes_no_list"
                            class="select2" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <?php 
                            $field = "is_active";
                        ?>
                        <x-Inputs.drop-down :name="$field" label="Active" :value="$search[$field]" :list="$yes_no_list"
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
@endsection
