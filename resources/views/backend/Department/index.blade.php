@extends($layout)

@section('content')
<<<<<<< HEAD

<?php
$page_header_links = [
    ["title" => "Create", "url" => route($routePrefix . ".create")]
];

?>

@include($partial_path . ".page_header")

<div class="card">
    <div class="card-body">
        <form method="GET" class="summary_search" action="{{ route($routePrefix . '.index') }}">
            <input type="hidden" name="is_sort_clear" value="1"/>
            <div class="row mb-4">
                <div class="col-md-3">
                    <x-Inputs.text-field name="name" label="Name" :value="$search['name']" autocomplete="off" />
                </div>
               
                <div class="col-md-3">
                    <x-Inputs.drop-down name="is_active" label="Active"
                        :value="$search['is_active']"
                        :list="$yes_no_list"
                        class="select2" />
                </div>
            </div>
            <x-Backend.summary-search-form-footer :selectedPaginationLimit="$pagination_limit" />
        </form>
    </div>
</div>

<div id="index_table">
    @include($viewPrefix . ".index_table")
</div>

@endsection
=======
    <?php
    $page_header_links = [['title' => 'Add', 'url' => route($routePrefix . '.create')]];
    $false = false;
    ?>

    <div class="row">
        <div class="col-xxxl-10">

            @include($partial_path . '.page_header')

            <div class="card hidden">
                <div class="card-body">
                    <form method="GET" class="summary_search" action="{{ route($routePrefix . '.index') }}">
                        <input type="hidden" name="is_sort_clear" value="1" />                        
                        <?php 
                            $field = "name";
                        ?>
                        <input type="text"  name="{{ $field }}" value="{{ $search[$field] }}" />

                        <?php 
                            $field = "short_name";
                        ?>
                        <input type="text"  name="{{ $field }}" value="{{ $search[$field] }}" />
                    </form>
                </div>
            </div>


            <div id="index_table">
                @include($viewPrefix . '.index_table')
            </div>
        </div>
    </div>
@endsection
>>>>>>> base_v3
