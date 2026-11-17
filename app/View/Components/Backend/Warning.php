<?php

namespace App\View\Components\Backend;

use App\View\Components\BackendComponent;

class Warning extends BackendComponent
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->view_path = 'warning';
    }
}
