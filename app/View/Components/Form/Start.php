<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Start extends Component
{
    public string $form_method = "";
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public String $action = "",
        public String $method = "GET",
        public $preventCssClassAdd = false,
    ) {

        if (in_array($method, ["GET"])) {
            $this->form_method = $method;
        } else {
            $this->form_method = "POST";
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.form.start');
    }
}
