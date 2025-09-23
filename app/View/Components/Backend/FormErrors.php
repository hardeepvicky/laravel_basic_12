<?php

namespace App\View\Components\Backend;

use App\View\Components\BackendComponent;
use Illuminate\View\Component;

class FormErrors extends BackendComponent
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
<<<<<<< HEAD
    public function __construct()
=======
    public function __construct(
        public array $customErrors = []
    )
>>>>>>> base_v3
    {
        parent::__construct();
        
        $this->view_path = 'form-errors';
    }
}
