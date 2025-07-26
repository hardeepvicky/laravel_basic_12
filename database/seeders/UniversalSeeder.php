<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class UniversalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call("json:model Department");
        Artisan::call("json:model SubDepartment");
        Artisan::call("json:model Designation");
    }
}
