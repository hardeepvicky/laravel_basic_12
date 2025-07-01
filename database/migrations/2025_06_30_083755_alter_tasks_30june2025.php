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
        Schema::table('tasks', function (Blueprint $table) {
            $table->integer("priority")->default(1)->after("end_date_time");
            $table->string("complete_time_in_days")->nullable()->after("end_date_time");
            $table->unsignedBigInteger("parent_id")->nullable()->after("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('priority');
            $table->dropColumn('complete_time_in_days');
            $table->dropColumn('parent_id');
        });
    }
};
