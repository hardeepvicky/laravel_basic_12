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
        Schema::create('task_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId("task_id")->constrained("tasks")->onDelete('cascade');
            $table->foreignId("lead_id")->constrained("leads")->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_customers');
    }
};
