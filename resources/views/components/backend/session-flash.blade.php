<?php 
if (Session::has('success')):
    $messages = Session::get('success'); 

    Session::forget('success');

    if (!is_array($messages))
    {
        $messages = [$messages];
    }
?>
    @foreach($messages as $msg)
    <div class="alert alert-success dark alert-dismissible fade show" role="alert">
        {{ $msg }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endforeach
<?php endif; ?>

<?php
if (Session::has('fail')):
    $messages = Session::get('fail'); 

    Session::forget('fail');

    if (!is_array($messages))
    {
        $messages = [$messages];
    }
?>
    @foreach($messages as $msg)
    <div class="alert alert-danger dark alert-dismissible fade show">
        {{ $msg }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>        
    </div>
    @endforeach
<?php endif; ?>
