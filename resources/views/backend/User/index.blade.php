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
                    <div class="col-md-3">
                        <x-Inputs.text-field name="name" label="Name" :value="$search['name']" autocomplete="off" />
                    </div>
                    <div class="col-md-3">
                        <x-Inputs.text-field name="email" label="Email" :value="$search['email']" autocomplete="off" />
                    </div>
                    <div class="col-md-3">
                        <x-Inputs.drop-down name="role_id" label="Role" :value="$search['role_id']" :list="$role_list"
                            class="select2" />
                    </div>
                    <div class="col-md-3">
                        <x-Inputs.drop-down name="is_active" label="Active" :value="$search['is_active']" :list="$yes_no_list"
                            class="select2" />
                    </div>
                    <div class="col-md-3">
                        <x-Inputs.drop-down id="type" name="type" label="Type" :list="$type_list" :value="$search['type']"
                            class="select2 cascade-drop-down" data-sr-cascade-target="#user_category_id"
                            data-sr-cascade-url="/user-category/ajax_get_list/{v}" />
                    </div>
                    <div class="col-md-3 type-block type-block-all">
                        <x-Inputs.drop-down id="user_category_id" name="user_category_id" label="Category" :value="$search['user_category_id']"
                            class="select2" />
                    </div>
                    <div class="col-md-3 type-block type-block-{{ User::TYPE_EXTERNAL }}">
                        <x-Inputs.drop-down id="country_id" name="country_id" label="Country" :list="$country_list"
                            :value="$search['country_id']" class="select2" />
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
        var my_country = JSON.parse('{!! json_encode($my_country) !!}');
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

            $(".cascade-drop-down").trigger("change", {
                pageLoad: true
            });
        });
    </script>
@endsection
