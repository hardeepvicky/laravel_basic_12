<?php

use App\Models\User;
?>

@extends($layout)

@section('content')
<?php
$page_header_links = [['title' => 'Create', 'url' => route($routePrefix . '.create')]];

?>

@include($partial_path . '.page_header')

<div class="card">
    <div class="card-body new-btn-showcase">
        <form method="GET" class="summary_search" action="{{ route($routePrefix . '.index') }}">
            <input type="hidden" name="is_sort_clear" value="1" />
            <div class="row mb-4">                
                <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                    <?php
                    $field = "name";
                    $label = 'Name';
                    ?>
                    <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]" />
                </div>
                <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                    <?php
                    $field = "email";
                    $label = 'Email';
                    ?>
                    <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]" />
                </div>
                <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                    <?php
                    $field = "mobile";
                    $label = 'Mobile';
                    ?>
                    <x-Inputs.text-field :name="$field" :label="$label" :value="$search[$field]" />
                </div>
                <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                    <?php
                    $field = "is_active";
                    $label = 'Active';
                    ?>
                    <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$yes_no_list"
                        class="select2" />
                </div>
                <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                    <?php
                    $field = "role_id";
                    $label = "Role";
                    ?>
                    <x-Inputs.drop-down :name="$field" :label="$label" :value="$search[$field]" :list="$role_list"
                        class="select2" />
                </div>
                <div class="col-md-4 col-lg-3 col-xxxl-2 mb-2">
                    <?php
                    $field = "type";
                    $label = "Type";
                    ?>
                    <x-Inputs.drop-down :id="$field" :name="$field" :label="$label" :value="$search[$field]"
                        :list="$type_list" class="select2" class="select2"
                    />
                </div>                
            </div>
            <x-Backend.summary-search-form-footer :selectedPaginationLimit="$pagination_limit" />
        </form>
    </div>
</div>

<div id="index_table">
    @include($viewPrefix . '.index_table')
</div>

<script>
    $.events.execAtLast(function() {
        $("#type").change(function() {
            var v = $(this).val();

            $(".type-block").hide();
            $(".type-block").find("input,select").attr("disabled", true);

            if (v) {
                var selector = ".type-block-all, .type-block-" + v;

                if ($(selector).length > 0) {
                    $(selector).show();
                    $(selector).find("input,select").removeAttr("disabled");
                }
            }

        }).trigger("change");

        $(".cascade-drop-down").trigger("sr-cascade.change", {
            pageLoad: true
        });        
    });
</script>
@endsection