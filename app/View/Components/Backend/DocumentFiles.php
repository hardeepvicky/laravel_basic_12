<?php

namespace App\View\Components\Backend;

use App\View\Components\BackendComponent;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DocumentFiles extends BackendComponent
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $records
    )
    {
        foreach($records as $k => $record)
        {
            $record->afterFind();
        }

        $this->view_path = "document-files";
    }
}
