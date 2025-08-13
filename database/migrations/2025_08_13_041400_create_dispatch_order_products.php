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
        Schema::create('dispatch_order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId("dispatch_order_id")->constrained("dispatch_orders")->onDelete("restrict");
            $table->foreignId("product_id")->constrained("products")->onDelete("restrict");
            $table->float("qty");
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
        Schema::dropIfExists('dispatch_order_products');
    }
};
