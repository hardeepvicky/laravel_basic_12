@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'Add', 'url' => route($routePrefix . '.create')]];
    $false = false;
    ?>

    @include($partial_path . '.page_header')

    <div class="card hidden">
        <div class="card-body">
            <form method="GET" class="summary_search" action="{{ route($routePrefix . '.index') }}">
                <input type="hidden" name="is_sort_clear" value="1" />
                <?php 
                    $field = "name";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                <?php 
                    $field = "product_category_id";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                <?php 
                    $field = "is_find_nested_child";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

                <?php 
                    $field = "model_name";
                ?>
                <input type="text" name="{{ $field }}" value="{{ $search[$field] }}" />

            </form>
        </div>
    </div>

    <div id="index_table">
        @include($viewPrefix . '.index_table')
    </div>

    <script>
        $(document).on("click", "a.mark_as_main_image", function(e){
            var url = $(this).attr("href");

            var main_image_id = $(this).data("main_image_id");

            ajaxGetJson(url, function(response)
            {
                $("#" + main_image_id).attr("href", response['url']);
                $("#" + main_image_id).find("img").attr("src", response['url']);
            });

            return false;
        });
    </script>
@endsection
