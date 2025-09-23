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
        Schema::create('dispatch_order_product_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignId("dispatch_order_product_id")->constrained("dispatch_order_products")->onDelete("restrict");
            $table->foreignId("product_serial_id")->constrained("product_serials")->onDelete("restrict");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatch_order_product_serials');
    }
};
