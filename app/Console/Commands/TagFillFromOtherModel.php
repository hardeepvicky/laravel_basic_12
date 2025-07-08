<?php

namespace App\Console\Commands;

use App\Helpers\ConsoleOutput;
use App\Helpers\LaravelExtend;
use App\Models\AppMenu;
use App\Models\AppMenuRelatedLink;
use App\Models\BaseModel;
use App\Models\BaseModelInterface;
use App\Models\BaseMongoModel;
use App\Models\City;
use App\Models\Country;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Enquiry;
use App\Models\ModuleLink;
use App\Models\RecurringTask;
use App\Models\Role;
use App\Models\RoleRouteName;
use App\Models\RouteName;
use App\Models\State;
use App\Models\SubDepartment;
use App\Models\Tag;
use App\Models\TagLink;
use App\Models\Task;
use App\Models\User;
use App\Models\UserAllowIp;
use App\Models\UserRole;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TagFillFromOtherModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tags:fill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tags Will be saved from all other models';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $main_counters = [
            "total" => 0,
            "insert" => 0,
            "update" => 0,
            "delete" => 0,
        ];

        $output = new ConsoleOutput();

        $model_list = [
            Department::class,
            Designation::class,
            SubDepartment::class,
            User::class,
            Country::class,
            State::class,
            RecurringTask::class,
            Task::class
        ];

        foreach($model_list as $model_class)
        {
            $main_model = new $model_class();

            $output->writeLogToConsole($model_class);

            if (!method_exists($model_class, "getListCache"))
            {
                $output->writeLogToConsole("method getListCache() not exist", "error");
                continue;
            }

            $name_field = "name";

            if (isset($model_class::$auto_tag_field) && $model_class::$auto_tag_field)
            {
                $name_field = $model_class::$auto_tag_field;
            }

            $table_info = $main_model->getTableInfo($main_model->getTable());

            // d(array_keys($table_info["columns"]));

            if  (!isset($table_info["columns"]["id"]) || !isset($table_info["columns"][$name_field]))
            {
                $output->writeLogToConsole("$model_class either field id or $name_field is missing", "error");
                continue;
            }

            $list = $model_class::getListCache("id", $name_field);

            try
            {
                $model_class_name = str_class_name_without_namespace($model_class);

                $counts = [
                    "total" => count($list),
                    "insert" => 0,
                    "update" => 0,
                ];

                $main_counters['total'] += $counts['total'];

                DB::beginTransaction();

                foreach($list as $id => $name)
                {
                    $condition_data = [
                        "main_model" => $model_class_name,
                        "main_table" => $main_model->getTable(),
                        "main_id" => $id
                    ];

                    $save_data = array_merge($condition_data, [
                        "name" => $name,
                    ]);

                    $tag = Tag::updateOrCreate($condition_data, $save_data);

                    if($tag->wasRecentlyCreated)
                    {
                        $counts['insert']++;
                        $main_counters['insert']++;
                    }
                    else
                    {
                        $counts['update']++;
                        $main_counters['update']++;
                    }
                }

                DB::commit();

                $counts = array_human_keys($counts);

                $output->addLog($counts);

                $output->writeLogToConsole();

                $output->clearLogs();
            }
            catch(Exception $ex)
            {
                DB::rollBack();

                $output->writeLogToConsole("$model_class  : " . $ex->getMessage(), "error");
                continue;
            }
        }

        $main_counters = array_human_keys($main_counters);

        $output->addLog($main_counters);

        $output->writeLogToConsole();

        $output->clearLogs();
    }
}

