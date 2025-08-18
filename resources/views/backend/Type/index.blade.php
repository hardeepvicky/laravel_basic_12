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
                <input name="{{ $field }}" value="{{ $search[$field] }}" />
                <?php 
                    $field = "type";
                ?>
                <input name="{{ $field }}" value="{{ $search[$field] }}" />
                <?php 
                    $field = "short_name";
                ?>
                <input name="{{ $field }}" value="{{ $search[$field] }}" />
                <?php 
                    $field = "code";
                ?>
                <input name="{{ $field }}" value="{{ $search[$field] }}" />
            </form>
        </div>
    </div>

    <div id="index_table">
        @include($viewPrefix . '.index_table')
    </div>

@endsection
