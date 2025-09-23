@if($withInfo)
<<<<<<< HEAD
    {{ $records->appends(request()->except('page'))->links('pagination::default-with-info') }}
@else
    {{ $records->appends(request()->except('page'))->links('pagination::default') }}
=======
    {{ $records->appends(request()->except(['page', 'full_view', "_"]))->links('pagination::default-with-info') }}
@else
    {{ $records->appends(request()->except(['page', 'full_view', "_"]))->links('pagination::default') }}
>>>>>>> base_v3
@endif