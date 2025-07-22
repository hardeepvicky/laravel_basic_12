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
        Schema::create('recurring_task_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId("recurring_task_id")->constrained("recurring_tasks")->onDelete('restrict');
            $table->foreignId("document_file_id")->constrained("document_files")->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_task_files');
    }
};
