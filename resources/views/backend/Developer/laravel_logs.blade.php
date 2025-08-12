@extends($layout)

@section('content')
    <div class="card">
        <div class="card-body">
            <div style="width : 500px; margin: auto;">
                <x-Inputs.drop-down id="file_name" name="file_name" label="File Name" :value="$search['file_name']" :list="$file_list"
                    class="select2" />
            </div>
        </div>
    </div>

    @if(isset($log_content))
    <pre>
        {!! $log_content !!}
    </pre>
    @endif

    <script>
        $(function() {
            $("#file_name").change(function() {
                var v = $(this).val();
                if (v) {
                    window.location.href = '{{ route($current_route_name) }}/' + v;
                }
            });
        })
    </script>
@endsection
