<?php

namespace App\Console\Commands;

use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Models\AppMenu;
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
        $model_name = $this->ask('Enter Model name');

        $model_name = trim($model_name);

        $filename = $model_name .".json";

        $path = "files/command/";

        $disk = Storage::disk(DEFAULT_DISK);

        $fileUtility = new FileUtility($disk);

        switch($model_name)
        {
            case "Type":
                $records = Type::all();
                $json = json_encode($records->toArray());                
                break;

            case "AppMenu":
                $records = AppMenu::all();
                $json = json_encode($records->toArray());                
                break;

            default:
                $this->error($model_name . " is not in switch case");
        }

        if (isset($json) && $json)
        {
            $fileUtility->writeLog($path, $filename, $json);
                    
            $this->info("$filename : Json Write Successfully");
        }
    }
}
