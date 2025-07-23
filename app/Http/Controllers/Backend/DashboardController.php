<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\DateUtility;
use App\Helpers\LaravelExtend;
use App\Models\Tag;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends BackendController
{
    public String $routePrefix = "dashbaord";

    public function index()
    {
        if (is_admin_role_login())
        {
            return $this->_admin();
        }
        else
        {
            return $this->_other();
        }
    }

    private function _admin()
    {
        $view_name = "admin";

        $task_figures = [
            'new' => [
                'label' => "New (Assigned)",
                'value' => 0
            ],
            'pending' => [
                'label' => "Pending (Confirm)",
                'value' => 0
            ],
            'in_progress' => [
                'label' => "In-Progress",
                'value' => 0
            ],
            'completed' => [
                'label' => "Completed",
                'value' => 0
            ],
            'verified' => [
                'label' => "Verified",
                'value' => 0
            ],
        ];

        $last_year_date = DateUtility::change(null, -1, DateUtility::YEARS, DateUtility::DATE_FORMAT);

        $default_conditions = [
            ["created_at", ">", $last_year_date],
        ];

        $task_figures['new']['value'] = Task::where($default_conditions)->where("status", Task::STATUS_ASSIGNED)->count();
        $task_figures['pending']['value'] = Task::where($default_conditions)->where("status", Task::STATUS_CONFIRM)->count();
        $task_figures['in_progress']['value'] = Task::where($default_conditions)->where("status", Task::STATUS_IN_PROGRESS)->count();
        $task_figures['completed']['value'] = Task::where($default_conditions)->where("is_completed", 1)->count();
        $task_figures['verified']['value'] = Task::where($default_conditions)->where("is_verified", 1)->count();

        $this->setForView(compact("task_figures", "last_year_date"));

        return $this->view($view_name);
    }

    private function _other()
    {
        $view_name = "default";

        $task_figures = [
            'rating' => [
                'label' => "Your Avg. Task Rating",
                'value' => 0
            ],
            'new' => [
                'label' => "New (Assigned)",
                'value' => 0
            ],
            'pending' => [
                'label' => "Pending (Confirm)",
                'value' => 0
            ],
            'in_progress' => [
                'label' => "In-Progress",
                'value' => 0
            ],
            'completed' => [
                'label' => "Completed",
                'value' => 0
            ],
            'verified' => [
                'label' => "Verified",
                'value' => 0
            ],
        ];

        $last_year_date = DateUtility::change(null, -1, DateUtility::YEARS, DateUtility::DATE_FORMAT);

        $default_conditions = [
            ["created_at", ">", $last_year_date],
            ["assign_to_user_id", LaravelExtend::getAuthUserId()],
        ];

        $task_figures['rating']['value'] = Task::where($default_conditions)->where('status', Task::STATUS_VERIFIED)->avg('rating');

        $task_figures['rating']['value'] = round($task_figures['rating']['value'], 2);

        $task_figures['new']['value'] = Task::where($default_conditions)->where("status", Task::STATUS_ASSIGNED)->count();
        $task_figures['pending']['value'] = Task::where($default_conditions)->where("status", Task::STATUS_CONFIRM)->count();
        $task_figures['in_progress']['value'] = Task::where($default_conditions)->where("status", Task::STATUS_IN_PROGRESS)->count();
        $task_figures['completed']['value'] = Task::where($default_conditions)->where("is_completed", 1)->count();
        $task_figures['verified']['value'] = Task::where($default_conditions)->where("is_verified", 1)->count();

        $this->setForView(compact("task_figures", "last_year_date"));

        return $this->view($view_name);
    }
}
