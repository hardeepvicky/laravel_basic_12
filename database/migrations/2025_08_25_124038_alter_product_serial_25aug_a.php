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
        if (Schema::hasColumn('product_serials', 'manufacture_date'))
        {
            $q = "ALTER TABLE `product_serials` CHANGE COLUMN `manufacture_date` `manufacture_date_time` DATETIME NULL DEFAULT NULL ;";

            DB::statement($q);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
