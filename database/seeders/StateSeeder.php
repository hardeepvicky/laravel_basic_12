<?php

namespace Database\Seeders;

use App\Helpers\ConsoleOutput;
use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $counts = [
            "total_count" => 0,
            "insert_count" => 0,
            "update_count" => 0,
            "delete_count" => 0,
        ];

        $records = State::get();

        $list = [];

        foreach ($records as $record) {
            if (!$record->short_name) {
                $list[$record->id] = State::createShortName($record->name);
            }
        }

        $counts['total_count'] = count($list);

        $duplicates = array_find_duplicates($list);

        $unique_list = array_except($list, $duplicates);

        foreach ($unique_list as $state_id => $short_name) {

            foreach ($records as $record) {
                if ($state_id == $record->id) {
                    $record->short_name = $short_name;
                    if ($record->save()) {
                        $counts['update_count']++;
                    }
                }
            }
        }

        $output = new ConsoleOutput();

        $counts = array_human_keys($counts);

        $output->addLog($counts);

        $output->writeLogToConsole();
    }
}
