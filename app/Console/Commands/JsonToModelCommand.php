<?php

namespace App\Console\Commands;

use App\Helpers\ConsoleOutput;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Helpers\MyCommand;
use App\Helpers\Util;
use App\Models\BaseModelInterface;
use Illuminate\Support\Facades\Storage;

class JsonToModelCommand extends MyCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'json:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'saving data to json files';

    public static string $path = "files/command/ModelToJsonCommand";

    public FileUtility $fileUtility;
    public array $log_info = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->logCreate([
            "command" => $this->signature,
            "description" => $this->description
        ]);

        $disk = ModelToJsonCommand::getStorageDisk();
        $this->fileUtility = new FileUtility($disk);

        $path = $this->fileUtility->getDisk()->path(ModelToJsonCommand::$path);

        if (empty($path)) {
            $this->info("there is no any folder at $path");
            return;
        }

        $files = FileUtility::getFileList($path, ["json"], false);

        if (empty($files)) {
            $this->info("No JSON file found");
            return;
        }

        foreach($files as $k => $file)
        {
            $files[$k] = pathinfo($file, PATHINFO_FILENAME);
        }

        $filename = $this->choice(
            'Select File :',
            $files,
        );

        if (!$filename) {
            return;
        }

        $file_path = ModelToJsonCommand::$path . "/" . $filename . ".json";

        $content = $this->fileUtility->getContent($file_path);

        $records = json_decode($content);

        if ($records === null && json_last_error() !== JSON_ERROR_NONE) {
            $this->error("cound not decode json from " . $filename);
        } else {

            $model_class = "App\\Models\\" . $filename;

            if (!class_exists($model_class))
            {
                $this->error("Class does not exist " . $model_class);
                return;
            }

            $db_count = $model_class::count();

            $user_input = $this->ask("Table have $db_count records, Do You still want to truncate (Yes/No) [Default:No]");

            $will_truncate = false;

            if (strtolower(trim($user_input)) == "yes") {
                $this->info("User reply : $user_input");
                $will_truncate = true;                
            }

            $this->save($filename, Util::objToArray($records), $will_truncate);
        }

        $this->logUpdate($this->log_info);
    }

    public function save($filename, array $records, bool $will_truncate = false)
    {
        $model_class = "App\\Models\\" . $filename;

        if (count($records) <= 0)
        {
            return 0;
        }
        
        $counters = [
            "total" => 0,
            "insert" => 0,
            "update" => 0,
            "delete" => 0,
            "truncate" => 0,
        ];

        if (isset($records[0]['parent_id']))
        {
            $records = Util::getTreeArray($records, "parent_id");
        }

        $count = count($records);

        $this->info("Working on $filename, " . " Count : " . $count);

        $db_count = $model_class::count();

        if ($will_truncate)
        {
            $this->info("truncating...");
            $model_class::truncate();
            $counters['truncate'] += $db_count;
        }
        else
        {
            $user_input = $this->ask("Table have $db_count records, where $count records will be save, Do You still want to proceed (Yes/No) [Default:Yes]");

            if (strtolower(trim($user_input)) == "no") {
                $this->info("User reply : $user_input");
                return;
            }
        }

        foreach($records as $tree_record)
        {
            $record = [];

            foreach($tree_record as $field => $value)
            {
                if (is_scalar($value))
                {
                    $record[$field] = $value;
                }
            }

            $is_insert = $is_update = false;

            if ($record)
            {
                $counters['total']++;

                $model_class::insertOrUpdate($record, $is_insert, $is_update);

                if ($is_insert)
                {
                    $counters['insert']++;
                }

                if ($is_update)
                {
                    $counters['update']++;
                }
            }
        }

        $this->log_info[$filename] = $counters;

        $counts = array_human_keys($counters);

        $output = new ConsoleOutput();

        $output->addLog($counts);

        $output->writeLogToConsole();
    }
}
