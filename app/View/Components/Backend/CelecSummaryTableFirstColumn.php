<?php

namespace App\View\Components\Backend;

use App\View\Components\BackendComponent;
use Closure;
use Illuminate\Contracts\View\View;

class CelecSummaryTableFirstColumn extends BackendComponent
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $record,
        public $routePrefix,
        public $buttons = ""
    )
    {
        $this->view_path = "celec-summary-table-first-column";
    }
}
