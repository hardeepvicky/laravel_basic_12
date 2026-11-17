<?php

namespace App\View\Components\Inputs;

use Illuminate\View\Component;

class DropDown extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public String $name,
        public String $label,        
        public $value = "",
        public array $list = [],
        public $mandatory = false,  
        public $is_disabled = false,  
        public $pleaseSelect = true,
        public $preventCssClassAddToInput = false,
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
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.inputs.drop-down');
    }
}
