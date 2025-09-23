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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId("purchase_order_id")->constrained("purchase_orders")->onDelete("restrict");
            $table->foreignId("item_id")->constrained("items")->onDelete("restrict");
            $table->foreignId("make_id")->nullable()->constrained("makers")->onDelete("restrict");
            $table->string("part_no", 45)->nullable();
            $table->float("qty");
            $table->float("receive_qty")->default(0);
            $table->foreignId("uom_id")->constrained("uoms")->onDelete("restrict");
            $table->float("rate");
            $table->float("amount");
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
        Schema::dropIfExists('purchase_order_items');
    }
};
