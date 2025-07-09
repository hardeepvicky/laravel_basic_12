<?php

namespace App\Console\Commands;

use App\Helpers\ConsoleOutput;
use App\Helpers\LaravelExtend;
use App\Helpers\MyCommand;
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

class TagFillFromOtherModel extends MyCommand
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
        $this->logCreate([
            "command" => $this->signature,
            "description" => $this->description
        ]);

        $main_counters = [
            "total" => 0,
            "insert" => 0,
            "update" => 0,
            "delete" => 0,
        ];

        $output = new ConsoleOutput();

        $model_list = LaravelExtend::getListOfModels();

        foreach($model_list as $model_name => $model_class)
        {
            $main_model = new $model_class();

            if ($model_name == "User" || $main_model instanceof BaseModelInterface)
            {

            }
            else
            {
                continue;
            }

            if (!method_exists($model_class, "getListCache"))
            {
                $output->writeLogToConsole("method getListCache() not exist", "error");
                continue;
            }

            if (isset($main_model->field_for_create_tag) && $main_model->field_for_create_tag)
            {
                $name_field = $main_model->field_for_create_tag;
            }
            else
            {
                continue;
            }

            $output->writeLogToConsole($model_class);
            $output->writeLogToConsole("Fetching Table Info");

            $table_info = $main_model->getTableInfo($main_model->getTable());

            if  (!isset($table_info["columns"]["id"]) || !isset($table_info["columns"][$name_field]))
            {
                $output->writeLogToConsole("$model_class either field id or $name_field is missing", "error");
                continue;
            }

            $list = $model_class::getList("id", $name_field);

            $id_to_delete = $model_class::getList("id", "id");

            try
            {
                $counts = [
                    "total" => count($list),
                    "insert" => 0,
                    "update" => 0,
                    "delete" => 0,
                ];

                $main_counters['total'] += $counts['total'];

                DB::beginTransaction();

                foreach($list as $id => $name)
                {
                    $save_data = [
                        "main_id" => $id,
                        "name" => $name,
                        "main_table_field" => $name_field
                    ];

                    $save_info = Tag::createTag($save_data, $main_model, null);

                    if(isset($save_info['is_insert']) && $save_info['is_insert'])
                    {
                        $counts['insert']++;
                        $main_counters['insert']++;
                    }
                    else if(isset($save_info['is_update']) && $save_info['is_update'])
                    {
                        $counts['update']++;
                        $main_counters['update']++;
                    }

                    if (isset($save_info['model']) && $save_info['model'])
                    {
                        unset($id_to_delete[$id]);
                    }
                    else
                    {
                        $id_to_delete = [];
                    }
                }

                if ($id_to_delete)
                {
                    $counts['delete'] = count($id_to_delete);

                    $main_counters['delete'] += $counts['delete'];

                    Tag::destroy($id_to_delete);
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

                $output->writeLogToConsole($ex->getMessage(), "error");

                continue;
            }
        }

        $main_counters = array_human_keys($main_counters);

        $output->addLog($main_counters);

        $output->writeLogToConsole();

        $output->clearLogs();

        $this->output_lines = $output->output_lines;

        $this->logUpdate([
            "counts" => $main_counters
        ]);
    }
}

