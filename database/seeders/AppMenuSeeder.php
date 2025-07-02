<?php

namespace Database\Seeders;

use App\Exceptions\UserException;
use App\Helpers\ConsoleOutput;
use App\Helpers\Util;
use App\Models\AppMenu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AppMenuSeeder extends Seeder
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

        $filename = $model_name . ".json";

        $path = "files/command/ModelToJsonCommand/";

        $disk = Storage::disk(DEFAULT_DISK);

        $content = $disk->get($path . $filename);

        $records = json_decode($content);

        if ($records === null && json_last_error() !== JSON_ERROR_NONE) {
            $output->addLogMessage("cound not decode json from " . $path . $filename, "error");
        } else {
            $counts['total_count'] = count($records);

            $model = new AppMenu();

            $is_insert = false;
            $is_update = false;

            $table_info = $model->getTableInfo($model->getTable());

            // dd($table_info);

            foreach ($records as $record) {

                $save = [];

                foreach ($table_info['columns'] as $field => $field_attr) {
                    if (in_array($field, ["id"])) {
                        continue;
                    }

                    if (isset($record->{$field})) {
                        $save[$field] = $record->{$field};
                    }
                }

                if (!isset($save['parent_id'])) {
                    $save['parent_id'] = 0;
                }

                try {
                    // dd($save);
                    $record = Util::objToArray($record);
                    $id = $model->insertOrUpdate($record, $is_insert, $is_update);

                    if ($is_insert) {
                        $counts['insert_count']++;
                    }

                    if ($is_update) {
                        $counts['update_count']++;
                    }
                } catch (UserException $ex) {
                    if (isset($ex->data['custom_errors']) && is_array($ex->data['custom_errors'])) {
                        foreach ($ex->data['custom_errors'] as $group => $errors) {
                            foreach ($errors as $error) {
                                $output->addLogMessage($group . " : " . $error['label'] . " : " . $error['msg'], "error");
                            }
                        }
                    }
                }
            }
        }

        $counts = array_human_keys($counts);

        $output->addLog($counts);

        $output->writeLogToConsole();
    }
}
