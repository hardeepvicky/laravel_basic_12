@extends($layout)

@section('content')
    <?php
    $page_header_links = [];
    $false = false;
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
                        <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                        <?php
                            $field = "contact_id";
                        ?>
                        <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                        <?php
                            $field = "status_type";
                        ?>
                        <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                        <?php
                            $field = "carrier_id";
                        ?>
                        <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                        <?php
                            $field = "docket_no";
                        ?>
                        <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                        <?php
                            $field = "tracking_no";
                        ?>
                        <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                        
                        <?php
                            $field = "builty_no";
                        ?>
                        <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                        <?php
                            $field = "state_id";
                        ?>
                        <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                        <?php
                            $field = "district_id";
                        ?>
                        <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                        <?php
                            $field = "city_id";
                        ?>
                        <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                        <?php
                            $field = "pincode";
                        ?>
                        <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                        <?php
                            $field = "from_date";
                        ?>
                        <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                        <?php
                            $field = "to_date";
                        ?>
                        <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                        <x-Backend.summary-search-form-footer :showPaginationDropDown="$false" />
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
            });
        })
    </script>
@endsection
