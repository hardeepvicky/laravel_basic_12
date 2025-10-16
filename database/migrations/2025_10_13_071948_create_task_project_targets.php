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
        Schema::create('task_project_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId("task_id")->constrained("tasks")->onDelete("restrict");
            $table->foreignId("project_target_id")->constrained("project_targets")->onDelete("restrict");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_project_targets');
    }
};
