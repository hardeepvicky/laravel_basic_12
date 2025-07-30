<?php

namespace App\Console\Commands;

use App\Helpers\ConsoleOutput;
use App\Helpers\LaravelExtend;
use App\Helpers\MyCommand;
use App\Models\BaseModelInterface;
use App\Models\Tag;
use Exception;
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

            $list = $model_class::getListCache("id", $name_field);

            $id_to_delete = $model_class::getListCache("id", "id");

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

