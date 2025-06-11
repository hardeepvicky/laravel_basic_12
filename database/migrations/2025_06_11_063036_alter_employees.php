<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("Alter table employees MODIFY COLUMN `type` VARCHAR(45) null");
        DB::statement("Alter table employees MODIFY COLUMN `current_address` VARCHAR(255) null");
        DB::statement("Alter table employees MODIFY COLUMN `permanent_address` VARCHAR(255) null");
        DB::statement("ALTER TABLE `employees` DROP FOREIGN KEY `employees_current_city_id_foreign`;");
        DB::statement("ALTER TABLE `employees` ADD CONSTRAINT `employees_current_city_id_foreign` FOREIGN KEY (`current_city_id`) REFERENCES `cities`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;");

        Schema::table('employees', function (Blueprint $table) {
            $table->string("mobile2")->nullable()->after("mobile");            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('mobile2');
        });
    }
};
 