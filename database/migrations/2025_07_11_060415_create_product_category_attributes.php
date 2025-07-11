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
        Schema::create('product_category_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_category_id")->constrained("product_categories")->onDelete('restrict');
            $table->string("name");
            $table->string("input_type");
            $table->string("dropdown_type")->nullable();
            $table->boolean("is_active")->default(1);
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
        Schema::dropIfExists('product_category_attributes');
    }
};
