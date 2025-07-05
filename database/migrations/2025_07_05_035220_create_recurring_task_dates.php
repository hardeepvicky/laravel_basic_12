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
        Schema::create('recurring_task_dates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("recurring_task_id");
            $table->timestamp("date");
            $table->unsignedBigInteger("task_id")->nullable();
            $table->timestamps();

            $table->foreign('recurring_task_id')->references('id')->on('recurring_tasks')->onDelete('restrict');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_task_dates');
    }
};
