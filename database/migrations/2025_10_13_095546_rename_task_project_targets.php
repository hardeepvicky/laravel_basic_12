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
        $this->dropColumnsWithIndex("task_project_targets", ["project_target_id"]);

        Schema::rename('task_project_targets', 'task_targets');

        Schema::table('task_targets', function (Blueprint $table) {
            $table->foreignId("target_id")->nullable()->after("id")->constrained("targets")->onDelete("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("task_targets", ["target_id"]);

        Schema::rename('task_targets', 'task_project_targets');
    }
};
