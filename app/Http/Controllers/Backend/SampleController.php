<?php

namespace App\Http\Controllers\Backend;

use App\Exports\SampleItem;
use Maatwebsite\Excel\Facades\Excel;

class SampleController extends BackendController
{
    public String $routePrefix = "sample";

    public function item()
    {
        return Excel::download(new SampleItem, 'items.xlsx');
    }
}
