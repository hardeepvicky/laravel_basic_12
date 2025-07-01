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
        Schema::table('users', function (Blueprint $table) {
            $table->string("whatsapp_mobile")->nullable()->after("mobile");
        });

        $q = "ALTER TABLE `users` ADD UNIQUE INDEX `mobile_UNIQUE` (`mobile` ASC);";
        DB::statement($q);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('whatsapp_mobile');
        });

        $q = "ALTER TABLE `users` DROP INDEX `mobile_UNIQUE`";

        DB::statement($q);
    }
};
