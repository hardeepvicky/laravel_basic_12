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
        Schema::create('export_order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId("export_order_id")->constrained("export_orders")->onDelete("restrict");
            $table->foreignId("product_id")->constrained("products")->onDelete("restrict");
            $table->foreignId("make_id")->constrained("makers")->onDelete("restrict");
            $table->float("order_qty");
            $table->float("available_qty")->default(0);
            $table->float("qty_to_manufacture")->default(0);
            $table->float("rate")->default(0);
            $table->float("amount")->default(0);
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
        Schema::dropIfExists('export_order_products');
    }
};
