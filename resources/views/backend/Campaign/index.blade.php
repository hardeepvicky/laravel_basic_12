@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'Add', 'url' => route($routePrefix . '.create')]];
    $false = false;
    ?>

    <div class="row">
        <div class="col-xxxl-12">
            @include($partial_path . '.page_header')

            <div class="card hidden">
                <div class="card-body">
                    <form method="GET" class="summary_search" action="{{ route($routePrefix . '.index') }}">
                        <input type="hidden" name="is_sort_clear" value="1" />
                        <?php 
                            $field = "name";
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

    <script>
        $(function(){
            $.events.execAtLast(function(){
                $("#state_id").trigger("sr-cascade.change", {
                    pageLoad: true
                });
            });
        })
    </script>
@endsection
