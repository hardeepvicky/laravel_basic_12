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
        Schema::create('product_issue_solutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("issue_id")->constrained("product_issues")->onDelete("restrict");
            $table->text("name");
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
        Schema::dropIfExists('product_issue_solutions');
    }
};
