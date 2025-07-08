<?php

use App\Helpers\MyMigration;
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
        $this->dropColumnsWithIndex("recurring_tasks", ["task_should_be_complete_in"]);

        Schema::table('recurring_tasks', function (Blueprint $table) {
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
        $this->dropColumnsWithIndex("recurring_tasks", ["duration", "duration_type", "dept_id"]);
    }
};
