<?php

use App\Helpers\MyMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends MyMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->string("party_order_ref_no")->after("order_no")->nullable();
            $table->float("total_amount")->after("carrier_id")->default(0);
            $table->float("discount_per")->after("total_amount")->default(0);
            $table->float("discount")->after("discount_per")->default(0);            
            $table->float("payable_amount")->after("discount")->default(0);
            $table->float("paid_amount")->after("payable_amount")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("sale_orders", [
            "party_order_ref_no",
            "total_amount",
            "discount_per",
            "discount",
            "payable_amount", 
            "paid_amount"
        ]);
    }
};
