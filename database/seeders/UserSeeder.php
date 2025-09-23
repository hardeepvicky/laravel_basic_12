<?php

namespace Database\Seeders;

<<<<<<< HEAD
use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\User;
=======
use App\Exceptions\UserException;
use App\Helpers\ConsoleOutput;
use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserCategory;
>>>>>>> base_v3
use App\Models\UserRole;
use Exception;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::where("code", "system_admin")->first();

<<<<<<< HEAD
        if (!$role)
        {
            throw new Exception("System Admin Role Not Found");
        }

        $user = User::where("name", "Admin")->first();

        if (!$user)
        {
            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('admin@123'),
                'dont_send_email' => 1,
                'is_active' => 1,
                'is_pre_defined' => 1,
            ]);

            UserRole::create([
                "user_id" => $user->id,
                "role_id" => $role->id,
            ]);
        }
=======
        if (!$role) {
            throw new UserException("Role Not Found");
        }

        $user_category =  UserCategory::where("type", User::TYPE_INTERNAL)->first();

        if (!$user_category) {
            throw new UserException("User Category Not Found");
        }

        $counts = [
            "total_count" => 0,
            "insert_count" => 0,
            "update_count" => 0,
            "delete_count" => 0,
        ];

        $records = [
            [
                'type' => User::TYPE_INTERNAL,
                "user_category_id" => $user_category->id,
                'name' => 'admin',
                'email' => 'admin@celec.com',
                'password' => bcrypt('admin@423'),
                'dont_send_email' => 1,
                'is_active' => 1,
                'is_pre_defined' => 0,
            ],                         
        ];

        $counts['total_count'] = count($records);

        $model = new User();

        foreach ($records as $record) {

            if (User::where("email", $record['email'])->count())
            {
                unset($record['password']);
            }

            $model = User::insertOrUpdate($record, $is_insert, $is_update);

            if  (!$model)
            {
                throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
            }

            $model->assignRoles([$role->id]);

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
>>>>>>> base_v3
    }
}
