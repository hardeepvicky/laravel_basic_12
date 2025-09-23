@if ($paginator->hasPages())
<nav aria-label="Page navigation">
    <ul class="pagination pagination-primary">
        @if ($paginator->onFirstPage())
        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
            <a class="page-link" href="javascript:void(0)">&lsaquo;</a>
        </li>
        @else
        <li class="page-item">
<<<<<<< HEAD
            <a class="page-link" href="{{ \Request::url() }}" rel="prev">First</a>
        </li>
        <li class="page-item">
            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
=======
            <a class="page-link" href="{{ get_current_path_url(['page' => 1 ]) }}" rel="prev">First</a>
        </li>
        <li class="page-item">
            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev"
                aria-label="@lang('pagination.previous')">&lsaquo;</a>
>>>>>>> base_v3
        </li>
        @endif

        @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
<<<<<<< HEAD
        <li class="page-link disabled" aria-disabled="true">
=======
        <li class="page-link d-none d-md-block disabled" aria-disabled="true">
>>>>>>> base_v3
            <a class="page-link" href="javascript:void(0)">{{ $element }}</a>
        </li>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
        @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
<<<<<<< HEAD
        <li class="page-item active" aria-current="page">
            <a class="page-link" href="javascript:void(0)">{{ $page }}</a>
        </li>
        @else
        <li class="page-item">
=======
        <li class="page-item d-none d-md-block active" aria-current="page">
            <a class="page-link" href="javascript:void(0)">{{ $page }}</a>
        </li>
        @else
        <li class="page-item d-none d-md-block">
>>>>>>> base_v3
            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
        </li>
        @endif
        @endforeach
        @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
        <li class="page-item">
<<<<<<< HEAD
            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
        </li>
        <li class="page-item">
            <a class="page-link" href="{{ get_current_path_url(['page' => $paginator->lastPage() ]) }}" rel="last">Last</a>
=======
            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next"
                aria-label="@lang('pagination.next')">&rsaquo;</a>
        </li>
        <li class="page-item">
            <a class="page-link" href="{{ get_current_path_url(['page' => $paginator->lastPage() ]) }}"
                rel="last">Last</a>
>>>>>>> base_v3
        </li>
        @else
        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
            <a class="page-link" href="javascript:void(0)">&rsaquo;</a>
        </li>
        @endif
    </ul>
</nav>
@endif