@extends($layout)

@section('content')
<?php
$page_header_links = [
    ['title' => 'Add', 'url' => route($routePrefix . '.create')],
];
$false = false;

?>

@include($partial_path . '.page_header')

<div class="card">
    <div class="card-body">
        <form method="GET" class="summary_search" action="{{ route($routePrefix . '.index') }}">
            <input type="hidden" name="is_sort_clear" value="1" />
            <div class="row mb-4">
                <div class="col-md-1">
                    <?php
                    $field = 'pagination_limit';
                    ?>
                    <x-Inputs.drop-down :name="$field" label="Limit" :list="$paginationList" :value="$pagination_limit"
                        :pleaseSelect="$false" class="pagination_limit" />
                </div>
                <div class="col-md-3">
                    <?php
                    $field = "type";
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$type_list"
                        class="select2" />
                </div>
                <div class="col-md-3">
                    <?php
                    $field = "name";
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]" />
                </div>
                <div class="col-md-3">
                    <?php
                    $field = "status";
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$status_list"
                        class="select2" />
                </div>
                <div class="col-md-3">
                    <?php
                    $field = "assign_to_user_id";
                    $label = get_value_from_list($field_labels, $field, $field);
                    ?>
                    <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$user_list"
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