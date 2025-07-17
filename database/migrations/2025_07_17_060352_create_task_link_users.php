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
        Schema::create('task_link_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId("task_id")->constrained("tasks")->onDelete('restrict');
            $table->foreignId("user_id")->constrained("users")->onDelete('restrict');
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
        Schema::dropIfExists('task_link_users');
    }
};
