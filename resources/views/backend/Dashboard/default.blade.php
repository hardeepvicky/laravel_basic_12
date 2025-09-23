@extends($layout)

@section('content')
<<<<<<< HEAD

<h1>Welcome To Default Screen</h1>

<div class="card-body">    
    <a class="btn btn-primary" href="/login">Goto Login</a>
    
    <a class="btn btn-secondary" href="/register">Goto Register</a>

    <a class="btn btn-primary" href="/theme">Goto Theme</a>  
    
    <a class="btn btn-light"  href="/test">Goto Test Page</a>
</div>

@endsection
=======
    <div class="container-fluid">
        @include($partial_path . '.page_header')

        <h3>Coming Soon</h3>
    </div>
@endsection
>>>>>>> base_v3
