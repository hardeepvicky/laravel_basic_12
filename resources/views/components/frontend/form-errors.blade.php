@if ($errors->any())

    <div class="card border-danger rounded-3">
        <div class="card-header">
            <h5 class="card-title">Errors</h5>
        </div>
        <div class="card-body">
            <ol class="text-danger">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ol>
        </div>
    </div>

@endif
