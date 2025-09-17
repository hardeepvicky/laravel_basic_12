@extends($layout)

@section('content')

    <?php
        $page_header_links = [];

        foreach($type_list as $route_prefix => $label)
        {
           $page_header_links[] = ['title' => 'Add ' . $label, 'url' => route($route_prefix . '.create')];
        }
    ?>

    @include($partial_path . '.page_header')

    <div class="card">
        <div class="card-header hidden card-no-border p-1">
            <form method="GET" class="summary_search" action="{{ route($current_route_name) }}">
                <input type="hidden" name="is_sort_clear" value="1" />
                <input type="hidden" name="value" value="{{ $selected_value ?? '' }}" />
                <?php 
                    $field = "type";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                
                <?php 
                    $field = "name";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                
                <?php 
                    $field = "state_id";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                
                <?php 
                    $field = "city_id";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                
                <?php 
                    $field = "mobile";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                
                <?php 
                    $field = "mobile2";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                
                <?php 
                    $field = "email";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                
                <?php 
                    $field = "from_last_followup_date_time";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                
                <?php 
                    $field = "to_last_followup_date_time";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                
                <?php 
                    $field = "from_next_followup_date_time";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                
                <?php 
                    $field = "to_next_followup_date_time";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                
                <?php 
                    $field = "product_id";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
                
                <?php 
                    $field = "status_type";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />
            </form>
        </div>
        <div class="card-body">
            <div class="index_table" id="index_table">
                @include($viewPrefix . '.index_table')
            </div>
        </div>
    </div>
@endsection
