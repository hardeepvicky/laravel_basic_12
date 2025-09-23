<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
<<<<<<< HEAD
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
=======
        $this->call(UserCategorySeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);

        $this->call(StateSeeder::class);
        $this->call(TypeSeeder::class);
        $this->call(DropDownValueSeeder::class);
        $this->call(AppMenuSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(FieldSettingSeeder::class);
        $this->call(NotificationSettingSeeder::class);
>>>>>>> base_v3
    }
}
