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
        Schema::create('sale_orders', function (Blueprint $table) {
            $table->id();
            $table->string("order_no", 80)->unique();
            $table->foreignId("branch_id")->constrained("branches")->onDelete("restrict");
            $table->foreignId("dealer_id")->nullable()->constrained("dealers")->onDelete("restrict");
            $table->foreignId("customer_id")->nullable()->constrained("customers")->onDelete("restrict");
            $table->string("status", 45)->nullable();
            $table->date("estimated_dispatch_date")->nullable();
            $table->dateTime("dispatch_date_time")->nullable();
            $table->foreignId("carrier_id")->nullable()->constrained("parties")->onDelete("restrict");
            $table->string("dealer_order_no", 80)->nullable();
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
        Schema::dropIfExists('sale_orders');
    }
};
