@if($withInfo)
    {{ $records->appends(request()->except(['page', 'full_view', "_"]))->links('pagination::default-with-info') }}
@else
    {{ $records->appends(request()->except(['page', 'full_view', "_"]))->links('pagination::default') }}
@endif