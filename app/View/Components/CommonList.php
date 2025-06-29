<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CommonList extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $headerText = "",
        public array $list,
        public String $type = "ol",
        public String $liClass = "",
        public bool $preventDefaultCssClass = false
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.common-list');
    }
}
