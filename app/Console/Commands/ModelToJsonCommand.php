<?php

namespace App\Console\Commands;

use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Models\AppMenu;
use App\Models\FieldSetting;
use App\Models\Task;
use App\Models\Type;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ModelToJsonCommand extends Command
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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $model_name_list = ['Type', 'AppMenu', 'FieldSetting', 'Role'];

        sort($model_name_list);

        $options = ['Manully Enter', 'All'];

        $options = array_merge($options, $model_name_list);

        $choice = $this->choice(
            'Select Model :',
            $options,
            0 // default index (optional)
        );

        $model_list = [];

        if ($choice == "Manully Enter") {
            $model_name = $this->ask('Enter Model name');
            $model_name = trim($model_name);

            $model_list = [$model_name];
        }
        if ($choice == "All") {
            $model_list = $model_name_list;
        } else {
            $model_list = [$choice];
        }

        foreach ($model_list as $model_name) {
            $filename = $model_name . ".json";

            $path = "files/command/ModelToJsonCommand";

            $disk = Storage::disk(DEFAULT_DISK);

            $fileUtility = new FileUtility($disk);

            $ref_model_class = "App\\Models\\" . $model_name;

            $this->info("Working in $model_name");

            $count = $ref_model_class::count();

            $this->info("Total Count : $count");

            if ($count > 10000) {
                $user_input = $this->ask('Records are more than 1000, Do You still want to proceed (Yes/NO) [Default:No]');

                if (strtolower(trim($user_input)) != "yes") {
                    return;
                }
            }

            $records = $ref_model_class::all();

            $json = json_encode($records->toArray());

            if (isset($json) && $json) {
                $fileUtility->writeLog($path, $filename, $json);

                $this->info("$filename : Json Write Successfully");
                $this->newLine(1);
            }
        }
    }
}
