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
                        <div class="row mb-4">
                            <div class="col-sm-6 col-md-3 col-xl-2 mb-2">
                                <?php
                                $field = 'pagination_limit';
                                ?>
                                <x-Inputs.drop-down :name="$field" label="Limit" :list="$paginationList" :value="$pagination_limit" :pleaseSelect="$false"
                                    class="pagination_limit" />
                            </div>            
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                <?php
                                $field = 'status_type';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$status_type_list"
                                    class="select2" />
                            </div>          
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                <?php
                                $field = 'carrier_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$carrier_list"
                                    class="select2" />
                            </div>      
                            <div class="col-md-6 col-xxl-2 mb-2">
                                <?php
                                $field = 'docket_no';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]" />
                            </div>
                            <div class="col-md-6 col-xxl-2 mb-2">
                                <?php
                                $field = 'tracking_no';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]" />
                            </div>
                            <div class="col-md-6 col-xxl-2 mb-2">
                                <?php
                                $field = 'builty_no';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]" />
                            </div>                            
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                <?php
                                $field = 'country_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$country_list"
                                        :value="$search[$field]" class="select2 cascade-drop-down" 
                                        data-sr-cascade-target="#state_id"
                                        data-sr-cascade-url="/state/ajax_get_list/{v}" 
                                        />
                            </div>
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                <?php
                                $field = 'state_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :list="$state_list"
                                        :value="$search[$field]" class="select2 cascade-drop-down" 
                                        data-sr-cascade-target="#district_id"
                                        data-sr-cascade-url="/district/ajax_get_list/{v}" 
                                        />
                            </div>
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                <?php
                                $field = 'district_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$empty_list" :value="$search[$field]"
                                    class="select2 cascade-drop-down" 
                                    data-sr-cascade-target="#city_id"
                                    data-sr-cascade-url="/city/ajax_get_list/0/{v}" 
                                />
                            </div>
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                <?php
                                $field = 'city_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :id="$field" :name="$field" :label="$label"
                                    :list="$empty_list" :value="$search[$field]"
                                    class="select2" 
                                />
                            </div>       
                            <div class="col-md-6 col-xxl-2 mb-2">
                                <?php
                                $field = 'pincode';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]" />
                            </div>                     
                            
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
                $("#state_id").trigger("change", {
                    pageLoad: true
                });
            });
        })
    </script>
@endsection
