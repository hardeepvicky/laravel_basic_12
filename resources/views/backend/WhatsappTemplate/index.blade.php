@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'Create', 'url' => route($routePrefix . '.create')]];
    
    ?>

    @include($partial_path . '.page_header')

    <div class="card">
        <div class="card-body">
            <form method="GET" class="summary_search" action="{{ route($routePrefix . '.index') }}">
                <input type="hidden" name="is_sort_clear" value="1" />
                <div class="row mb-4">
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <?php
                        $field = 'name';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.text-field :id="$field" :name="$field" :label="$label" :value="$search[$field]" autocomplete="off" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <?php
                        $field = 'language';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.text-field :id="$field" :name="$field" :label="$label" :value="$search[$field]" autocomplete="off" />
                    </div>
                    <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                        <?php
                        $field = 'is_active';
                        $label = get_value_from_list($field_labels, $field, $field);
                        ?>
                        <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :value="$search[$field]" :list="$yes_no_list" class="select2" />
                    </div>
                </div>
                <x-Backend.summary-search-form-footer :selectedPaginationLimit="$pagination_limit" :paginationListOptions="$paginationList" />
            </form>
        </div>
    </div>

    <div id="index_table">
        @include($viewPrefix . '.index_table')
    </div>
@endsection
