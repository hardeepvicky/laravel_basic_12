<?php

namespace App\Console\Commands;

use App\Exceptions\UserException;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Helpers\MyCommand;
use App\Models\AppMenu;
use App\Models\BaseModelInterface;
use App\Models\FieldSetting;
use App\Models\Task;
use App\Models\Type;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ModelToJsonCommand extends MyCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model:json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'saving data to json files';

    public static string $path = "files/command/ModelToJsonCommand";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->logCreate([
            "command" => $this->signature,
            "description" => $this->description
        ]);

        $options = [];
        
        $model_list = LaravelExtend::getListOfModels();

        foreach($model_list as $model_name => $model_class)
        {
            $main_model = new $model_class();

            if ($model_name == "User" || $main_model instanceof BaseModelInterface)
            {
                $options[] = $model_name;
            }
            else
            {
                continue;
            }
        }

        $choice = $this->choice(
            'Select Model :',
            $options,
        );

        $model_name = $choice;

        $model_class = $model_list[$choice];

        $this->info("Working on $model_name");

        $count = $model_class::count();

        $main_counters[$model_name] = $count;

        if ($count > 1000) {
            $user_input = $this->ask('Records are more than 1000, Do You still want to proceed (Yes/NO) [Default:No]');

            if (strtolower(trim($user_input)) != "yes") {
                $this->info("User reply : $user_input");
                return;
            }
        }

        $records = $model_class::all();

        // foreach ($records as $k => $record) {
        //     $child_records = [];

        //     if (property_exists($model_class, "child_model_class")) 
        //     {
        //         if (isset($model_class::$child_model_class) && is_array($model_class::$child_model_class)) {
        //             foreach ($model_class::$child_model_class as $child_model_class => $child_model_arr) {
        //                 if ($child_model_class != $model_class) {
        //                     $child_model_class_name = str_class_name_without_namespace($child_model_class);

        //                     $conditions = [];
        //                     if (isset($child_model_arr['foreignKey']) && is_array($child_model_arr['foreignKey'])) {
        //                         foreach ($child_model_arr['foreignKey'] as $foreignKey) {
        //                             if (is_string($foreignKey)) {
        //                                 $conditions[] = [$foreignKey, $record->id];
        //                             }
        //                         }
        //                     } else {
        //                         $conditions[] = [$child_model_arr['foreignKey'], $record->id];
        //                     }

        //                     $qb = $child_model_class::where($conditions);

        //                     $count = $qb->count();

        //                     $main_counters[$child_model_class_name] = $count;

        //                     if ($count > 1000) {
        //                         $this->info("Working on $child_model_class_name");
        //                     }

        //                     $child_records[$child_model_class_name] = $qb->get()->toArray();
        //                 }
        //             }
        //         }
        //     }

        //     $records[$k]->child_records = $child_records;
        // }

        $this->writeJson($records->toArray(), $model_name);

        $this->logUpdate($main_counters);
    }

    public function writeJson(array $records, $filename)
    {
        $json = json_encode($records);

        if (isset($json) && strlen($json) > 0) {

            $disk = self::getStorageDisk();

            $fileUtility = new FileUtility($disk);

            $filename = $filename . ".json";

            $fileUtility->writeContent(self::$path, $filename, $json);

            $this->info("$filename : Json Write Successfully");
            $this->newLine(1);
        }
    }

    public static function getStorageDisk()
    {
        $disk = Storage::disk("public");

        return $disk;
    }
}
