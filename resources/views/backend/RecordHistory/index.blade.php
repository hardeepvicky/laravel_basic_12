@extends($layout)

@section('content')
    <?php
    $page_header_links = [];
    $false = false;
    ?>

    @include($partial_path . '.page_header')

    <div class="card">
        <div class="card-body">
            <form method="GET" class="summary_search" action="{{ route($routePrefix . '.index') }}">
                <input type="hidden" name="is_sort_clear" value="1" />
                <div class="row mb-4">
                    <div class="col-md-3 col-xxl-1 mb-2">
                        <?php
                        $field = 'pagination_limit';
                        ?>
                        <x-Inputs.drop-down :name="$field" label="Limit" :list="$paginationList" :value="$pagination_limit" :pleaseSelect="$false"
                            class="pagination_limit" />
                    </div>
                    <div class="col-md-2 col-xxl-1 mb-2">
                        <?php
                        $field = 'model_id';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]"/>
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxl-2 mb-2">
                        <?php
                        $field = 'model_display_name';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]"/>
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxl-2 mb-2">
                        <?php
                        $field = 'model_class';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$model_list"
                            class="select2" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxl-2 mb-2">
                        <?php
                        $field = 'action';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$action_list"
                            class="select2" />
                    </div>
                    
                    <div class="col-md-4 col-lg-3 col-xxl-2 mb-2">
                        <x-Inputs.text-field id="from_date" name="from_date" label="From Date"
                            :value="$search['from_date']" class="form-control date-picker" data-date-end="input#to_date"
                            autocomplete="off" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxl-2 mb-2">
                        <x-Inputs.text-field id="to_date" name="to_date" label="To Date" :value="$search['to_date']"
                            class="form-control date-picker" data-date-start="input#from_date" autocomplete="off" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxl-2 mb-2">
                        <?php
                        $field = 'created_by';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$userListCache"
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
