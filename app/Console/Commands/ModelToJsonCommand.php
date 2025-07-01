<?php

namespace App\Console\Commands;

use App\Helpers\FileUtility;
use App\Models\Task;
use App\Models\Type;
use Illuminate\Console\Command;

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

        $model_name = strtolower(trim($model_name));

        switch($model_name)
        {
            case "type":
                $path = "files/command/";

                $records = Type::all();

                $json = json_encode($records->toArray());

                $disk = Type::getStorageDisk();

                $fileUtility = new FileUtility($disk);

                $filename = "type.json";

                $fileUtility->writeLog($path, $filename, $json);
                
                $this->info("Json Write Successfully");
                break;
        }
    }
}
