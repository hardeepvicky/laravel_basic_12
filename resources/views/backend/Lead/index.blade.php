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
                            <div class="col-md-6 col-xxl-2 mb-2">
                                <?php
                                $field = 'name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]" />
                            </div>
                            <div class="col-md-6 col-xxl-2 mb-2">
                                <?php
                                $field = 'mobile';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]" />
                            </div>
                            <div class="col-md-6 col-xxl-2 mb-2">
                                <?php
                                $field = 'mobile2';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]" />
                            </div>
                            <div class="col-md-6 col-xxl-2 mb-2">
                                <?php
                                $field = 'email';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]" />
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
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                <?php
                                $field = 'source_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$source_list"
                                    class="select2" />
                            </div>
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                <?php
                                $field = 'source_type';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$source_type_list"
                                    class="select2" />
                            </div>
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                <?php
                                $field = 'deal_type';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$deal_type_list"
                                    class="select2" />
                            </div>
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                <?php
                                $field = 'dealer_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$dealer_list"
                                    class="select2" />
                            </div>
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                <?php
                                $field = 'campaign_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$campaign_list"
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
