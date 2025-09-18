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
        $this->dropColumnsWithIndex("sale_orders", [
            "paid_amount",
        ]);

        Schema::table('sale_orders', function (Blueprint $table) {
            $table->float("advance_amount")->after("payable_amount")->default(0);
            $table->text("terms")->after("remarks")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("sale_orders", [
            "advance_amount",
            "terms",
        ]);
    }
};
