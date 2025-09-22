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
        Schema::create('production_target_completes', function (Blueprint $table) {
            $table->id();
            $table->foreignId("production_target_id")->constrained("production_targets")->onDelete('restrict');
            $table->foreignId("complete_by_employee_id")->constrained("employees")->onDelete('restrict');
            $table->float("qty");
            $table->text("remarks")->nullable();
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
        Schema::dropIfExists('production_target_completes');
    }
};
