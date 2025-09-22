<?php

use App\Models\User;
?>

@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'Create', 'url' => route($routePrefix . '.create')]];
    ?>

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
                $field = "type";
                ?>
                <input type="text"  name="{{ $field }}" value="{{ $search[$field] }}" />

                <?php
                $field = "branch_id";
                ?>
                <input type="text"  name="{{ $field }}" value="{{ $search[$field] }}" />

                <?php
                $field = "department_id";
                ?>
                <input type="text"  name="{{ $field }}" value="{{ $search[$field] }}" />

                <?php
                $field = "designation_id";
                ?>
                <input type="text"  name="{{ $field }}" value="{{ $search[$field] }}" />
                
                <?php 
                    $field = "mobile";
                ?>
                <input type="text"  name="{{ $field }}" value="{{ $search[$field] }}" /> 
                
                <x-Backend.summary-search-form-footer :selectedPaginationLimit="$pagination_limit" />
            </form>
        </div>
    </div>

    <div id="index_table">
        @include($viewPrefix . '.index_table')
    </div>

@endsection