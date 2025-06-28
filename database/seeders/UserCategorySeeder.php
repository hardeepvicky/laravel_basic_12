<?php

namespace Database\Seeders;

use App\Helpers\ConsoleOutput;
use App\Models\Role;
use App\Models\User;
use App\Models\UserCategory;
use Illuminate\Database\Seeder;

class UserCategorySeeder extends Seeder
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

        $records = [
            [
                "code" => "dealer",
                "type" => User::TYPE_EXTERNAL,
                "name" => "Dealer",
                "is_active" => 1,
                "is_pre_defined" => 1
            ],
            [
                "code" => "demo-user",
                "type" => User::TYPE_EXTERNAL,
                "name" => "Demo User",
                "is_active" => 1,
                "is_pre_defined" => 1
            ]
        ];

        $counts['total_count'] = count($records);

        $user_category_model = new UserCategory();

        foreach ($records as $record) {
            $user_category_model->insertOrUpdate($record, $is_insert, $is_update);

            if ($is_insert) {
                $counts['insert_count']++;
            }

            if ($is_update) {
                $counts['update_count']++;
            }
        }

        $output = new ConsoleOutput();

        $counts = array_human_keys($counts);

        $output->addLog($counts);

        $output->writeLogToConsole();
    }
}
