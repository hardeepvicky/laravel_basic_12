@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'Add', 'url' => route($routePrefix . '.create')]];
    $false = false;
    ?>

    <div class="row">
        <div class="col-xxxl-12">
            @include($partial_path . '.page_header')

            <div class="card">
                <div class="card-body">
                    <form method="GET" class="summary_search" action="{{ route($routePrefix . '.index') }}">
                        <input type="hidden" name="is_sort_clear" value="1" />

                        <div class="hidden">
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
                                $field = "country_id";
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
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                <x-Inputs.text-field id="from_date" name="from_date" label="From Created Date"
                                    :value="$search['from_date']" class="form-control date-picker" data-date-end="input#to_date"
                                    autocomplete="off" />
                            </div>
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                <x-Inputs.text-field id="to_date" name="to_date" label="To Created Date" :value="$search['to_date']"
                                    class="form-control date-picker" data-date-start="input#from_date" autocomplete="off" />
                            </div>
                        </div>
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
