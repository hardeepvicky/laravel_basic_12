<?php

namespace App\View\Components\Backend;

use App\View\Components\BackendComponent;

class Information extends BackendComponent
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public array $info
    )
    {
        parent::__construct();
        $this->view_path = 'information';
    }
}
