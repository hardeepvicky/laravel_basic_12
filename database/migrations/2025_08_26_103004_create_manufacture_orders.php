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
        Schema::create('manufacture_orders', function (Blueprint $table) {
            $table->id();
            $table->string("order_from", 45);
            $table->dateTime("order_date_time");
            $table->foreignId("sale_order_product_id")->nullable()->constrained("sale_order_products")->onDelete('restrict');
            $table->foreignId("product_id")->constrained("products")->onDelete('restrict');
            $table->foreignId("make_id")->constrained("makers")->onDelete('restrict');
            $table->foreignId("assign_to_user_id")->nullable()->constrained("users")->onDelete('restrict');
            $table->float("order_qty");
            $table->float("manufactured_qty")->default(0);
            $table->dateTime("manufacture_complete_date_time")->nullable();
            $table->foreignId("manufacture_complete_by")->nullable()->constrained("users")->onDelete('restrict');
            $table->string("status", 80)->nullable();
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
        Schema::dropIfExists('manufacture_orders');
    }
};
