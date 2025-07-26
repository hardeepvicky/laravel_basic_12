<?php
$false = false;
$paginationList = [
    10 => 10,
    15 => 15,
    20 => 20,
    25 => 25,
    30 => 30,
    40 => 40,
    50 => 50,
];
?>

<form method="GET" class="summary_search" action="{{ route($current_route_name) }}">
    <input type="hidden" name="is_sort_clear" value="1" />
    <input type="hidden" name="value" value="{{ $selected_value ?? '' }}" />
    <div class="row m-1">
        <div class="col-sm-6 col-md-2 col-lg-1 mb-2">
            <?php
            $field = 'pagination_limit';
            ?>
            <x-Inputs.drop-down :name="$field" label="Limit" :list="$paginationList" :value="$pagination_limit" :pleaseSelect="$false"
                class="pagination_limit" />
        </div>
        <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
            <?php
            $field = 'type';
            $label = get_value_from_list($field_labels, $field, $field);
            ?>
            <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$type_list"
                class="select2" />
        </div>
        <div class="col-md-12 col-lg-6 col-xxxl-3 mb-2">
            <?php
            $field = 'document_category_id';
            $label = get_value_from_list($field_labels, $field, $field);
            $category_list = [0 => '-'] + $category_list;
            ?>
            <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$category_list"
                class="select2" />

            <?php
            $field = 'is_find_nested_child';
            ?>
            <x-Inputs.checkbox :name="$field" label="Find Nested Child" :value="$search[$field]" />
        </div>
        <div class="col-md-6 col-lg-3 col-xxxl-2 mb-2">
            <?php
            $field = 'name';
            $label = get_value_from_list($field_labels, $field, $field);
            ?>
            <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]" />
        </div>
        <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
            <label class="form-label">Sort</label>
            <div class="d-flex">
                <?php
                $field = 'sort_by';
                $label = get_value_from_list($field_labels, $field, $field);
                ?>
                <x-Inputs.drop-down :name="$field" :label="$false" :value="$sort_by" :list="$sort_by_list"
                    class="input-inline" />
                <?php
                $field = 'sort_dir';
                $label = get_value_from_list($field_labels, $field, $field);
                ?>
                <x-Inputs.drop-down :name="$field" :label="$false" :value="$sort_dir" :list="$sort_dir_list"
                    class="input-inline" :pleaseSelect="$false" />
            </div>
        </div>
    </div>
    <x-Backend.summary-search-form-footer :showPaginationDropDown="$false" />
</form>
