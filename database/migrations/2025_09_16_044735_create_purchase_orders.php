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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string("order_no", 45)->unique();
            $table->foreignId("branch_id")->constrained("branches")->onDelete("restrict");
            $table->foreignId("party_id")->constrained("parties")->onDelete("restrict");
            $table->float("total_amount")->default(0);
            $table->float("advance_amount")->default(0);
            $table->string("status", 45);
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
        Schema::dropIfExists('purchase_orders');
    }
};
