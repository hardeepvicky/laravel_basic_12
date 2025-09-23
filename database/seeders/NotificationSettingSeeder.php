<?php

namespace Database\Seeders;

use App\Console\Commands\ModelToJsonCommand;
use App\Exceptions\UserException;
use App\Helpers\ConsoleOutput;
use App\Helpers\Util;
use App\Models\AppMenu;
use App\Models\FieldSetting;
use App\Models\NotificationSetting;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class NotificationSettingSeeder extends Seeder
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

        $model_name = "NotificationSetting";

        $filename = $model_name . ".json";

        $path = ModelToJsonCommand::$path . "/";

        $disk = ModelToJsonCommand::getStorageDisk();

        $content = $disk->get($path . $filename);

        $records = json_decode($content);

        if ($records === null && json_last_error() !== JSON_ERROR_NONE) {
            $output->addLogMessage("cound not decode json from " . $path . $filename, "error");
        } else {
            $counts['total_count'] = count($records);

            $is_insert = false;
            $is_update = false;

            // dd($table_info);

            foreach ($records as $record) {

                try {
                    $record = Util::objToArray($record);
                    unset($record['id']);
                    unset($record['_id']);
                    
                    $model = NotificationSetting::insertOrUpdate($record, $is_insert, $is_update);

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
