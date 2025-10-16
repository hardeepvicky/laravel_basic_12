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
        Schema::create('project_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId("project_id")->constrained("projects")->onDelete("restrict");
            $table->string("name");
            $table->date("end_date")->nullable();
            $table->integer("list_display_order");
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
        Schema::dropIfExists('project_targets');
    }
};
