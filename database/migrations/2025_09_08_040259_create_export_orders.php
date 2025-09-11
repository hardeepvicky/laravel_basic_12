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
        Schema::create('export_orders', function (Blueprint $table) {
            $table->id();
            $table->string("order_no", 80)->unique();
            $table->string("party_order_ref_no")->nullable();
            $table->string("order_from_type", 45);
            $table->foreignId("export_contact_id")->nullable()->constrained("export_contacts")->onDelete("restrict");
            $table->foreignId("exp_customer_id")->nullable()->constrained("exp_customers")->onDelete("restrict");
            $table->foreignId("branch_id")->constrained("branches")->onDelete("restrict");
            $table->string("status", 45)->nullable();
            $table->date("estimated_dispatch_date")->nullable();
            $table->dateTime("dispatch_date_time")->nullable();
            $table->foreignId("carrier_id")->nullable()->constrained("exp_parties")->onDelete("restrict");
            $table->float("total_amount")->default(0);
            $table->float("discount_per")->default(0);
            $table->float("discount")->default(0);
            $table->float("payable_amount")->default(0);
            $table->float("paid_amount")->default(0);
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
        Schema::dropIfExists('export_orders');
    }
};
