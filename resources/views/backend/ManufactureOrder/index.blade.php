@extends($layout)

@section('content')
    <?php
    $page_header_links = [];
    ?>

    <div class="row">
        <div class="col-xxxl-12">
            @include($partial_path . '.page_header')

            <div class="card hidden">
                <div class="card-body">
                    <form method="GET" class="summary_search" action="{{ route($routePrefix . '.index') }}">
                        <input type="hidden" name="is_sort_clear" value="1" />
                        <?php 
                            $field = "sale_order_no";
                        ?>
                        <input type="text"  name="{{ $field }}" value="{{ $search[$field] }}" />

                        <?php 
                            $field = "product_id";
                        ?>
                        <input type="text"  name="{{ $field }}" value="{{ $search[$field] }}" />

                        <?php 
                            $field = "make_id";
                        ?>
                        <input type="text"  name="{{ $field }}" value="{{ $search[$field] }}" />

                        <?php 
                            $field = "assign_to_user_id";
                        ?>
                        <input type="text"  name="{{ $field }}" value="{{ $search[$field] }}" />

                        <?php 
                            $field = "status";
                        ?>
                        <input type="text"  name="{{ $field }}" value="{{ $search[$field] }}" />

                        <?php 
                            $field = "from_order_date_time";
                        ?>
                        <input type="text"  name="{{ $field }}" value="{{ $search[$field] }}" />

                        <?php 
                            $field = "to_order_date_time";
                        ?>
                        <input type="text"  name="{{ $field }}" value="{{ $search[$field] }}" />

                        <?php 
                            $field = "from_manufacture_complete_date_time";
                        ?>
                        <input type="text"  name="{{ $field }}" value="{{ $search[$field] }}" />

                        <?php 
                            $field = "to_manufacture_complete_date_time";
                        ?>
                        <input type="text"  name="{{ $field }}" value="{{ $search[$field] }}" />
                    </form>
                </div>
            </div>

            <div id="index_table">
                @include($viewPrefix . '.index_table')
            </div>
        </div>
    </div>
    
    <script>
        $(function(){
            $.events.execAtLast(function(){
                $("#state_id").trigger("sr-cascade.change", {
                    pageLoad: true
                });
            })
        });
    </script>
@endsection
