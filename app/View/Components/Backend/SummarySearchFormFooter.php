<?php

<<<<<<< HEAD
namespace App\View\Components\backend;
=======
namespace App\View\Components\Backend;
>>>>>>> base_v3

use App\View\Components\BackendComponent;

class SummarySearchFormFooter extends BackendComponent
{
    public function __construct(
<<<<<<< HEAD
        public $selectedPaginationLimit,
        public $paginationListOptions = null,
    )
    {
=======
        public $selectedPaginationLimit = null,
        public $paginationListOptions = null,
        public $showPaginationDropDown = true
    ) {
>>>>>>> base_v3
        parent::__construct();

        $paginationList = [
            10 => 10,
            20 => 20,
            50 => 50,
            100 => 100,
        ];

<<<<<<< HEAD
        if ($this->paginationListOptions)
        {
            if (is_string($this->paginationListOptions))
            {
                $paginationList = explode(",", $this->paginationListOptions);
                $paginationList = array_combine($paginationList, $paginationList);
            }
            else if (is_array($this->paginationListOptions))
            {
=======
        if ($this->paginationListOptions) {
            if (is_string($this->paginationListOptions)) {
                $paginationList = explode(",", $this->paginationListOptions);
                $paginationList = array_combine($paginationList, $paginationList);
            } else if (is_array($this->paginationListOptions)) {
>>>>>>> base_v3
                $paginationList = $this->paginationListOptions;
            }
        }

        $this->setForView(compact("paginationList"));
<<<<<<< HEAD
        
=======

>>>>>>> base_v3
        $this->view_path = "summary-search-form-footer";
    }
}
