<?php

namespace Database\Seeders;

use App\Helpers\ConsoleOutput;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
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
                "code" => "system_admin",
                'name' => 'Admin',
                'is_active' => 1,
                'is_pre_defined' => 1
            ],
            [
                "code" => "developer",
                'name' => 'Developer',
                'is_active' => 1,
                'is_pre_defined' => 1
            ],
            [
                "code" => "staff",
                'name' => 'Staff',
                'is_active' => 1,
                'is_pre_defined' => 1
            ],
            [
                'code' => 'external_user',
                'name' => 'External User',
                'is_active' => 1,
                'is_pre_defined' => 1
            ],
        ];

        $counts['total_count'] = count($records);

        foreach ($records as $record) {
            Role::insertOrUpdate($record, $is_insert, $is_update);

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
