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
            <div class="row mb-2">
                <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                    <?php
                        $field = "name";
                    ?>
                    <x-Inputs.text-field :name="$field" label="Name" :value="$search[$field]" />
                </div>
                <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                    <?php
                        $field = "subject";
                    ?>
                    <x-Inputs.text-field :name="$field" label="Subject" :value="$search[$field]" />
                </div>                
            </div>
            <x-Backend.summary-search-form-footer :selectedPaginationLimit="$pagination_limit" />
        </form>
    </div>
</div>

<div id="index_table">
    @include($viewPrefix . '.index_table')
</div>
@endsection