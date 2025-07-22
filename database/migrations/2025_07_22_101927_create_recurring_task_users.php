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
        Schema::create('recurring_task_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId("recurring_task_id")->constrained("recurring_tasks")->onDelete('restrict');
            $table->foreignId("user_id")->constrained("users")->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_task_users');
    }
};
