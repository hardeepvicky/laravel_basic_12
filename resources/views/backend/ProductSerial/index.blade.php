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
                <div class="hidden">
                    <?php 
                        $field = "serial_no";
                    ?>
                    <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                    <?php 
                        $field = "product_category_id";
                    ?>
                    <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                    <?php 
                        $field = "product_id";
                    ?>
                    <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                    <?php 
                        $field = "make_id";
                    ?>
                    <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                </div>

                <div class="row">
                    <div class="col-md-12 col-lg-6 col-xxxl-3 mb-2">
                        <?php
                        $field = 'product_category_id';
                        $label = "Category";
                        $category_list = [0 => '-'] + $product_category_list;
                        ?>
                        <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$category_list"
                            class="select2" />

                        <?php
                        $field = 'is_find_nested_child';
                        ?>
                        <x-Inputs.checkbox :name="$field" label="Find Nested Child" :value="$search[$field]" />
                    </div>
                </div>
                <x-Backend.summary-search-form-footer :showPaginationDropDown="$false" />
            </form>
        </div>
    </div>

    <div id="index_table">
        @include($viewPrefix . '.index_table')
    </div>
    <script>
        $(function() {
            $.events.execAtLast(function() {

                $("#product_category_id").trigger("sr-cascade.change", {
                    pageLoad: true
                });
            });
        });
    </script>
@endsection
