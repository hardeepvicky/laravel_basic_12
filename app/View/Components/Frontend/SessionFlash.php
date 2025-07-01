<?php

namespace App\View\Components\Backend;

use App\View\Components\FrontendComponent;

class SessionFlash extends FrontendComponent
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->view_path = "session-flash";
    }
}
