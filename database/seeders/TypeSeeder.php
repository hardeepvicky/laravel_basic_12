<?php

namespace Database\Seeders;

use App\Helpers\ConsoleOutput;
use App\Models\Type;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $output = new ConsoleOutput();

        $counts = [
            "total_count" => 0,
            "insert_count" => 0,
            "update_count" => 0,
            "delete_count" => 0,
        ];

        $model_name = "Type";

        $filename = $model_name .".json";

        $path = "files/command/";

        $disk = Storage::disk(DEFAULT_DISK);

        $content = $disk->get($path . $filename);

        $records = json_decode($content);

        if ($records === null && json_last_error() !== JSON_ERROR_NONE)
        {
             $output->addLogMessage("cound not decode json from " . $path . $filename, "error");
        }
        else
        {
            $counts['total_count'] = count($records);

            $model = new Type();

            $is_insert = false;
            $is_update = false;

            $table_info = Type::getTableInfo($model->getTable());

            // dd($table_info);

            foreach ($records as $record) {

                $save = [];

                foreach($table_info['columns'] as $field => $field_attr)
                {
                    if (in_array($field, ["id"]))
                    {
                        continue;
                    }

                    if (isset($record->{$field}))
                    {
                        $save[$field] = $record->{$field};
                    }
                }

                // dd($save);
                $id = $model->insertIgnoreIfExist($save, $is_insert);

                if ($id)
                {
                    $counts['insert_count']++;
                }
            }
        }

        $counts = array_human_keys($counts);

        $output->addLog($counts);

        $output->writeLogToConsole();
    }
}
