<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //assing_to_user_id

        if (Schema::hasColumn("lead_histories", "assing_to_user_id"))
        {
            Schema::table('lead_histories', function (Blueprint $table) {
                $table->renameColumn('assing_to_user_id', 'assign_to_user_id');            
            });
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
