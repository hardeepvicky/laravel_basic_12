@extends($layout)

@section('content')
    @include($partial_path . '.page_header')

    <div class="card hidden">
        <div class="card-body">
            <form method="GET" class="summary_search" action="{{ route($routePrefix . '.index') }}">
                <input type="hidden" name="is_sort_clear" value="1" />
                <?php 
                    $field = "branch_id";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                <?php 
                    $field = "bin_id";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                <?php 
                    $field = "item_id";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
            </form>
        </div>
    </div>

    <div id="index_table">
        @include($viewPrefix . '.transactions_table')
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
