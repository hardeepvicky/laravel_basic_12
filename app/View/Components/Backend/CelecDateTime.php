<?php

namespace App\View\Components\Backend;

use App\View\Components\BackendComponent;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CelecDateTime extends BackendComponent
{
    /**
     * Create a new component instance.
     */
    public function __construct(
            public $label = "",
            public $value = ""
    )
    {
        //

        $this->view_path = "celec-date-time";
    }
}
