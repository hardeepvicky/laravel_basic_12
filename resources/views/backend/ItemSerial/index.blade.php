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
                    $field = "item_id";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                <?php 
                    $field = "item_brand_id";
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

    @if( request()->has('last_insert_id') )
    <script>
        $(function(){
            localStorage.setItem("last_product_serial_insert_id", "{!! request('last_insert_id') !!}");
            window.close();
        });
    </script>
    @endif
@endsection
