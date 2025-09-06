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
        Schema::create('sale_order_product_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignId("sale_order_product_id")->constrained("sale_order_products")->onDelete("restrict");
            $table->foreignId("product_serial_id")->constrained("product_serials")->onDelete("restrict");
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
        Schema::dropIfExists('sale_order_product_serials');
    }
};
