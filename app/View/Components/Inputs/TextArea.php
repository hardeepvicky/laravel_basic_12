<?php

namespace App\View\Components\Inputs;

use Illuminate\View\Component;

class TextArea extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public String $name,
<<<<<<< HEAD
        public String $label,        
        public $value = "" ,
        public $mandatory = false,  
        public $errorName = ""      
=======
        public String $label,
        public $value = "" ,
        public $mandatory = false,
        public $preventCssClassAddToInput = false,
        public $errorName = "",
        public $layout="",
        public $horizontalFirstColumnLength="3",
        public $horizontalSecondColumnLength="9",
>>>>>>> base_v3
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
        return view('components.inputs.text-area');
    }
}
