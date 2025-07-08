<?php

use App\Helpers\MyMigration;
use Dom\Mysql;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends MyMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->integer("duration")->nullable()->after("start_date_time");
            $table->string("duration_type")->nullable()->after("duration");
            $table->unsignedBigInteger("dept_id")->nullable()->after("id");
            $table->foreign('dept_id')->references('id')->on('departments')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("tasks", ["duration", "duration_type", "dept_id"]);
    }
};
