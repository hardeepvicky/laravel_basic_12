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
        Schema::dropIfExists('product_attributes');
        
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id")->constrained("products")->onDelete('restrict');
            $table->foreignId("product_category_attribute_id")->constrained("product_category_attributes")->onDelete('restrict');
            $table->text("name");
            $table->text("value");
            $table->integer("display_order")->default(0);
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
        Schema::dropIfExists('product_attributes');
    }
};
