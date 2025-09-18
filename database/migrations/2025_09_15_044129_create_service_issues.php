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
        Schema::create('service_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId("service_id")->constrained("services")->onDelete("restrict");
            $table->string("issue_type", 45);
            $table->foreignId("issue_id")->nullable()->constrained("product_issues")->onDelete("restrict");
            $table->text("issue")->nullable();
            $table->string("solution_type", 45);
            $table->foreignId("issue_solution_id")->nullable()->constrained("product_issue_solutions")->onDelete("restrict");
            $table->text("solution")->nullable();
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
        Schema::dropIfExists('service_issues');
    }
};

