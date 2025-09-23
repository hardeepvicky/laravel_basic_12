@extends($layout)

@section('content')
<<<<<<< HEAD

<div class="my-2 pt-2">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php
                    $ajax_view_name = 'errors.ajax.' . $error_code;

                    if (!view()->exists($ajax_view_name))
                    {
                        $ajax_view_name = 'errors.ajax.default';
                    }
                ?>
                @include($ajax_view_name)
            </div>
        </div>
    </div>
</div>

@endsection
=======
    <div class="pt-2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <?php
                    if (!isset($ajax_view_name))
                    {
                        $ajax_view_name = 'errors.ajax.default';
                    }
                    ?>
                    @include($ajax_view_name)                    
                </div>
            </div>
        </div>
    </div>
@endsection
>>>>>>> base_v3
