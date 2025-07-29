@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'Add', 'url' => route($routePrefix . '.create')]];
    $false = false;
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
                    <div class="col-md-6 col-lg-4 col-xxl-3 mb-2">
                        <?php 
                            $field = "parent_id";
                            $label = get_value_from_list($field_labels, $field, $field);
                            $tree_list = [0 => "-"] + $tree_list;
                        ?>
                        <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$tree_list"
                            class="select2" />

                        <?php 
                            $field = "is_find_nested_child";
                        ?>
                        <x-Inputs.checkbox :name="$field" label="Find Nested Child" :value="$search[$field]" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <?php
                        $field = 'name';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <?php
                        $field = 'is_last_node';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$yes_no_list"
                            class="select2" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <?php
                        $field = 'is_active';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$success_fail_list"
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
