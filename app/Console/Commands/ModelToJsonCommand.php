<?php

namespace App\Console\Commands;

use App\Exceptions\UserException;
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

    public static string $path = "files/command/ModelToJsonCommand";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $default_model_name_list = ['Type', 'AppMenu', 'FieldSetting', 'Role'];

        sort($default_model_name_list);

        $options = ['Manully Enter', 'All'];

        $options = array_merge($options, $default_model_name_list);

        $choice = $this->choice(
            'Select Model :',
            $options,
            0 // default index (optional)
        );

        $model_list = [];

        if ($choice == "Manully Enter") {
            $model_name = $this->ask('Enter Model name');
            $model_name = trim($model_name);

            if (strlen($model_name) <= 0) {
                return;
            }

            $model_list = [$model_name];
        } else if ($choice == "All") {
            $model_list = $default_model_name_list;
        } else {
            $model_list = [$choice];
        }

        foreach ($model_list as $model_name) {
            $ref_model_class = "App\\Models\\" . $model_name;

            $this->info("Working on $model_name");

            $count = $ref_model_class::count();

            $this->info("Total Count : $count");

            if ($count > 1000) {
                $user_input = $this->ask('Records are more than 1000, Do You still want to proceed (Yes/NO) [Default:No]');

                if (strtolower(trim($user_input)) != "yes") {
                    $this->info("User reply : $user_input");
                }
            }

            $records = $ref_model_class::all();

            foreach ($records as $k => $record) {
                $child_records = [];

                $child_model = new $ref_model_class();

                if (!property_exists($ref_model_class, "child_model_class")) {
                    throw new UserException("child_model_class Array is not set in $ref_model_class");
                }

                if (isset($ref_model_class::$child_model_class) && is_array($ref_model_class::$child_model_class)) {
                    foreach ($ref_model_class::$child_model_class as $child_model_class => $child_model_arr) {
                        if ($child_model_class != $ref_model_class) {
                            $child_model_class_name = str_class_name_without_namespace($child_model_class);

                            $this->info("Working on $child_model_class_name");

                            $conditions = [];
                            if (isset($child_model_arr['foreignKey']) && is_array($child_model_arr['foreignKey'])) {
                                foreach ($child_model_arr['foreignKey'] as $foreignKey) {
                                    if (is_string($foreignKey)) {
                                        $conditions[] = [$foreignKey, $record->id];
                                    }
                                }
                            } else {
                                $conditions[] = [$child_model_arr['foreignKey'], $record->id];
                            }

                            $qb = $child_model_class::where($conditions);

                            $count = $qb->count();

                            $this->info("Total Count : $count");

                            if ($count > 1000) {
                                $user_input = $this->ask('Records are more than 1000, Do You still want to proceed (Yes/NO) [Default:No]');

                                if (strtolower(trim($user_input)) != "yes") {
                                    $this->info("User reply : $user_input");
                                }
                            }

                            $child_model_class_name = str_class_name_without_namespace($child_model_class);
                            $child_records[$child_model_class_name] = $qb->get()->toArray();
                        }
                    }
                }

                $records[$k]->child_records = $child_records;
            }

            $this->writeJson($records->toArray(), $model_name);
        }
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
