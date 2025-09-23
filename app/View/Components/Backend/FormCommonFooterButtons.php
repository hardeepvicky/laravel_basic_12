<?php

<<<<<<< HEAD
namespace App\View\Components\backend;

use App\View\Components\BackendComponent;
use Illuminate\View\Component;
=======
namespace App\View\Components\Backend;

use App\View\Components\BackendComponent;
>>>>>>> base_v3

class FormCommonFooterButtons extends BackendComponent
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public $buttons = null
<<<<<<< HEAD
    )
    {
        parent::__construct();

        $buttonList = ["submit", "submit_and_redirect_to_summary", "reset"];

        if ($buttons)
        {
            if (is_string($buttons))
            {
                $buttonList = explode(",", $buttons);
            }
            else if (is_array($buttons))
            {
=======
    ) {
        parent::__construct();

        $buttonList = ["submit", "submit_and_redirect_to_summary", "submit_and_redirect_to_edit", "submit_and_add"];

        if ($buttons) {
            if (is_string($buttons)) {
                $buttonList = explode(",", $buttons);
            } else if (is_array($buttons)) {
>>>>>>> base_v3
                $buttonList = $buttons;
            }
        }

        $this->setForView(compact("buttonList"));

        $this->view_path = "form-common-footer-buttons";
    }
}
