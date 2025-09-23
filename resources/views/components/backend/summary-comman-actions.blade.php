<ul class="action">
    
    @if(in_array("show", $buttonList))
    <li>
        <a class="btn btn-outline-info summary-action-button" href="{{ route($routePrefix . '.show',[$id]) }}">
            <i class="icon-pencil-alt"></i>
        </a>
    </li>
    @endif

<<<<<<< HEAD
=======
    @if(in_array("history", $buttonList))
    <li>
        <a class="btn btn-outline-info summary-action-button" href="{{ route($routePrefix . '.history',[$id]) }}">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </a>
    </li>
    @endif

>>>>>>> base_v3
    @if(in_array("edit", $buttonList))
    <li>
        <a class="btn btn-outline-primary summary-action-button" href="{{ route($routePrefix . '.edit',[$id]) }}">
            <i class="icon-pencil-alt"></i>
        </a>
    </li>
    @endif

<<<<<<< HEAD
=======
    @if(in_array("copy", $buttonList))
    <li>
        <a class="btn btn-outline-primary summary-action-button" href="{{ route($routePrefix . '.copy',[$id]) }}">
            <i class="fa-solid fa-copy"></i>
        </a>
    </li>
    @endif

>>>>>>> base_v3
    @if(in_array("delete", $buttonList))
    <li>
        <x-Backend.summary-delete-button url="{{ route($routePrefix . '.destroy', [$id]) }}" />
    </li>
    @endif

    @if(in_array("pdf", $buttonList))
    <li>
        <a class="btn btn-outline-info summary-action-button" href="{{ route($routePrefix . '.pdf', [$id]) }}" >
            <i class="fa-solid fa-file-pdf"></i>
        </a>
    </li>
    @endif

    @if(in_array("print", $buttonList))
    <li>
        <a class="btn btn-outline-info summary-action-button" href="{{ route($routePrefix . '.print', [$id]) }}" >
            <i class="fa-solid fa-print"></i> Print
        </a>
    </li>
    @endif
<<<<<<< HEAD
=======

    @if(in_array("tr_details", $buttonList))
    <li>
        <span class="btn btn-outline-light text-dark summary-action-button css-toggler"
            data-sr-css-class-toggle-target="#record-{{ $id }}"
            data-sr-css-class-toggle-class="hidden">
            <i class="fa-solid fa-circle-info"></i>
        </span>
    </li>
    @endif
>>>>>>> base_v3
</ul>