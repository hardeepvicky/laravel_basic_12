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
        Schema::create('sale_order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId("sale_order_id")->constrained("sale_orders")->onDelete("restrict");
            $table->foreignId("product_id")->constrained("products")->onDelete("restrict");
            $table->foreignId("make_id")->constrained("makers")->onDelete("restrict");
            $table->float("order_qty");
            $table->float("available_qty")->default(0);
            $table->float("qty_to_manufacture")->default(0);
            $table->text("remarks")->nullable();
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
        Schema::dropIfExists('sale_order_products');
    }
};
