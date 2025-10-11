<?php

namespace App\Livewire\Widget;

use App\Models\ViewItemStockGroupByCategory;
use App\Models\ViewProductStockGroupByCategory;
use Livewire\Component;

class StockInventory extends Component
{
    public $data;

    public $isLoading = false;

    public function mount()
    {
        $this->fetchData();
    }

    public function fetchData()
    {
        $this->data['stocks'] = ViewProductStockGroupByCategory::orderBy("sum_qty", "DESC")->get()->toArray();

        $temp = ViewItemStockGroupByCategory::orderBy("sum_qty", "DESC")->get()->toArray();

        $this->data['inventory'] = [];

        foreach($temp as $arr)
        {
            $this->data['inventory'][$arr['unit']][] = $arr;
        }
    }

    public function render()
    {
        return view('livewire.widget.stock-inventory');
    }
}
