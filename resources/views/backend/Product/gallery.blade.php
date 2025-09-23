@extends($layout)

@section('content')
    <div class="card">
        <div class="card-header card-no-border p-1">
            <div class="d-md-flex d-sm-block align-items-center">
                <div class="flex-grow-1">
                    <?php
                    $paginationList = [
                        10 => 10,
                        12 => 12,
                        15 => 15,
                        18 => 18,
                        20 => 20,
                        25 => 25,
                        30 => 30,
                        40 => 40,
                        50 => 50,
                    ];
                    ?>

                    <form method="GET" class="summary_search" action="{{ route($current_route_name) }}"
                        data-load_html_at="#{{ $index_table_id }}">
                        <input type="hidden" name="is_sort_clear" value="1" />
                        <input type="hidden" name="value" value="{{ $selected_value ?? '' }}" />
                        <div class="row m-1">
                            
                            <div class="col-md-12 col-lg-6 col-xxxl-3 mb-2">
                                <?php
                                $field = 'product_category_id';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]"
                                    :list="$product_category_list" class="select2" />
                            </div>
                            <div class="col-md-6 col-lg-3 col-xxxl-2 mb-2">
                                <?php
                                $field = 'model_name';
                                $label = get_value_from_list($field_labels, $field, $field);
                                ?>
                                <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]" />
                            </div>
                            <div class="col-sm-6 col-md-3 col-xl-2 mb-2">
                                <?php
                                $field = 'pagination_limit';
                                ?>
                                <x-Inputs.drop-down :name="$field" label="Limit" :list="$paginationList" :value="$pagination_limit"
                                    :pleaseSelect="$false" class="pagination_limit" />
                            </div>
                            <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                                <label class="form-label">Sort</label>
                                <div class="d-flex">
                                    <?php
                                    $field = 'sort_by';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.drop-down :name="$field" :label="$false" :value="$sort_by"
                                        :list="$sort_by_list" class="input-inline" />
                                    <?php
                                    $field = 'sort_dir';
                                    $label = get_value_from_list($field_labels, $field, $field);
                                    ?>
                                    <x-Inputs.drop-down :name="$field" :label="$false" :value="$sort_dir"
                                        :list="$sort_dir_list" class="input-inline" :pleaseSelect="$false" />
                                </div>
                            </div>
                        </div>
                        <x-Backend.summary-search-form-footer :showPaginationDropDown="$false" />
                    </form>

                </div>
                <div class="flex-shrink-0 text-end">
                    <div class="d-inline-flex m-1">
                        <a target="_blank" href="{{ route($routePrefix . ".create") }}" class="btn btn-outline-secondary" >
                            Add
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="{{ $index_table_id }}" class="index_table">
                @include($viewPrefix . '.gallery_records')
            </div>
        </div>
    </div>

    <script>
        $(function(){
            @if(!$multiple)
            $("input.product").change(function()
            {
                if (this.checked)
                {
                    $("input.product").prop("checked", false);
                    $(this).prop("checked", true);
                }
            });
            @endif
        });
    </script>
@endsection
