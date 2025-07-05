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
        Schema::dropIfExists('task_recurring_dates');

        $this->dropColumnsWithIndex("tasks", ["parent_id", "code", "type", "one_time_type", "recurring_type", "complete_time_in_days"]);
        
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger("recurring_task_id")->nullable()->after("id");
            $table->string("task_type", 100)->nullable()->after("recurring_task_id");

            $table->foreign('recurring_task_id')->references('id')->on('recurring_tasks')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("tasks", ["recurring_task_id", "task_type"]);
    }
};
