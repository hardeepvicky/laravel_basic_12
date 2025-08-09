<?php

namespace App\Livewire\Widget;

use App\Helpers\DateUtility;
use App\Models\DashboardCounter;
use App\Models\Lead;
use App\Models\Type;
use Livewire\Component;
use Carbon\Carbon;

class LeadCounter extends Component
{
    public $period = 'this_week';

    public $data;

    public $isLoading = false;

    public function mount()
    {
        $this->fetchData();
    }

    public function updatedPeriod()
    {
        $this->fetchData();
    }

    public function fetchData()
    {
        $this->isLoading = true;

        $startDate = $endDate = null;        

        switch ($this->period) {
            case 'this_week':
                $startDate = Carbon::now()->startOfWeek();                
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();                
                break;
            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'last_3_months':
                $startDate = Carbon::now()->subMonths(3)->startOfMonth();                
                break;
            case 'last_6_months':
                $startDate = Carbon::now()->subMonths(6)->startOfMonth();                
                break;
            case 'last_12_months':
                $startDate = Carbon::now()->subMonths(12)->startOfMonth();                
                break;
        }

        // --- MOCK DATA ---
        // Replace this with your actual database query, e.g.,
        // $this->data = YourModel::whereBetween('created_at', [$startDate, $endDate])->get();
        //
        // For this example, we'll return some mock data.
        $this->data = [
            'period' => $this->period,
            'date_range' => [
                'start' => $startDate ? $startDate->format(DateUtility::DATE_OUT_FORMAT) : "",
                'end' => $endDate ? $endDate->format(DateUtility::DATE_OUT_FORMAT) : "Till Now",
            ],
            'counters' => [],
            'change_counters' => [],
        ];

        $cache_key = "status_type_active_list";

        $status_type_list = Type::getCache($cache_key);

        if (!$status_type_list)
        {
            $type_conditions = [
                'type' => Type::getType($this, "status_type")
            ];

            $status_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

            Type::addCache($cache_key, $status_type_list, 60 * 60 * 24);
        }

        foreach($status_type_list as $status => $status_label)
        {
            $builder = Lead::where("status_type", $status);

            if ($startDate)
            {
                $builder->where("created_at" , ">=", $startDate->format(DateUtility::DATE_FORMAT));
            }

            if ($endDate)
            {
                $builder->where("created_at" , "<=", $endDate->format(DateUtility::DATE_FORMAT));
            }

            $count = $builder->count();

            $this->data['counters'][] = [
                'label' => $status_label,
                "count" => $count
            ];
        }

        $dasboard_counters = DashboardCounter::where("model", "Lead")->orderBy("display_order", "ASC")->orderBy("label", "ASC")->get();

        foreach($dasboard_counters as $dasboard_counter)
        {
            $this->data['change_counters'][] = $dasboard_counter->toArray();
        }

        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.widget.lead-counter');
    }
}
