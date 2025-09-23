<?php

namespace App\View\Components\Inputs;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Choice extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public String $name,
        public String $label = "",
        public $value = "",
        public array $list = [],
        public $mandatory = false,
        public $preventCssClassAddToInput = false,
        public $isDisabled = false,
        public $errorName = "",
        public $layout="",
        public $horizontalFirstColumnLength="3",
        public $horizontalSecondColumnLength="9",
    )
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.inputs.choice');
    }
}
