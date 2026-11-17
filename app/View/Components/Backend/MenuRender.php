<?php

namespace App\View\Components\Backend;

use App\View\Components\BackendComponent;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MenuRender extends BackendComponent
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public array $menus,
        public int $level = 0
    )
    {
        $this->view_path = "menu-render";
    }
}
