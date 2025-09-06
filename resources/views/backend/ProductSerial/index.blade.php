@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'Add', 'url' => route($routePrefix . '.create')]];
    $false = false;
    ?>

    @include($partial_path . '.page_header')

    <div class="card hidden">
        <div class="card-body">
            <form method="GET" class="summary_search" action="{{ route($routePrefix . '.index') }}">
                <input type="hidden" name="is_sort_clear" value="1" />
                <?php 
                    $field = "serial_no";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                <?php 
                    $field = "product_category_id";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                <?php 
                    $field = "is_find_nested_child";
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
