@extends($layout)

@section('content')
    <?php
    $page_header_links = [];
    $false = false;    
    ?>

    @include($partial_path . '.page_header')

    <div class="card">
        <div class="card-body">
            <form method="GET" class="summary_search" action="{{ route($routePrefix . '.my') }}">
                <input type="hidden" name="is_sort_clear" value="1" />
                <div class="row mb-2">
                    <div class="col-sm-6 col-md-3 col-xl-2 mb-2">
                        <?php
                        $field = 'pagination_limit';
                        ?>
                        <x-Inputs.drop-down :name="$field" label="Limit" :list="$paginationList" :value="$pagination_limit" :pleaseSelect="$false"
                            class="pagination_limit" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <?php
                        $field = 'sub_type';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$notification_type_list"
                            class="select2" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <?php
                        $field = 'from_user_id';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$from_list"
                            class="select2" />
                    </div>                    
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <x-Inputs.text-field id="from_date" name="from_date" label="From Date"
                            :value="$search['from_date']" class="form-control date-picker" data-date-end="input#to_date"
                            autocomplete="off" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <x-Inputs.text-field id="to_date" name="to_date" label="To Date" :value="$search['to_date']"
                            class="form-control date-picker" data-date-start="input#from_date" autocomplete="off" />
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <?php 
                            $field = "unread_only";
                        ?>
                        <x-Inputs.checkbox :name="$field" label="Show Only unread" :value="$search[$field]" :hiddenField="$false" />
                    </div>
                </div>
                <x-Backend.summary-search-form-footer :showPaginationDropDown="$false" />
            </form>
        </div>
    </div>

    <div id="index_table">
        @include($viewPrefix . '.my_table')
    </div>
@endsection
