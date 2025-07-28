<?php

namespace App\View\Components\Widget;

use App\Helpers\DateUtility;
use App\Models\Task;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TaskStatusChart extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $last_year_date = DateUtility::change(null, -1, DateUtility::YEARS, DateUtility::DATE_FORMAT);

        $default_conditions = [
            ["created_at", ">", $last_year_date],
        ];

        $records = [];

        $qb = Task::where($default_conditions)->where("status", Task::STATUS_ASSIGNED);

        $records[] = [
            'label' => "New (Assigned)",
            'value' => $qb->count()
        ];

        $qb = Task::where($default_conditions)->where("status", Task::STATUS_CONFIRM);

        $records[] = [
            'label' => "Pending (Confirm)",
            'value' => $qb->count()
        ];

        $qb = Task::where($default_conditions)->where("status", Task::STATUS_IN_PROGRESS);

        $records[] = [
            'label' => "In-Progress",
            'value' => $qb->count()
        ];

        $qb = Task::where($default_conditions)->where("status", Task::STATUS_COMPLETED);

        $records[] = [
            'label' => "Completed",
            'value' => $qb->count()
        ];

        $qb = Task::where($default_conditions)->where("status", Task::STATUS_VERIFIED);

        $records[] = [
            'label' => "Verified",
            'value' => $qb->count()
        ];

        return view('components.widget.task-status-chart', compact("records"));
    }
}
