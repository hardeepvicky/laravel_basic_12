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
        Schema::create('task_todos', function (Blueprint $table) {
            $table->id();
            $table->foreignId("task_id")->constrained("tasks")->onDelete("restrict");
            $table->string("todo");
            $table->boolean("is_completed")->default(0);
            $table->dateTime("complete_date_time")->nullable();
            $table->timestamps();
            $table->bigInteger("created_by")->nullable();
            $table->bigInteger("updated_by")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_todos');
    }
};
