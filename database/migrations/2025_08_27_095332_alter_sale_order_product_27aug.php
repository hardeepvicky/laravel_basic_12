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
        Schema::table('sale_order_products', function (Blueprint $table) {
            $table->float("rate")->after("qty_to_manufacture")->default(0);
            $table->float("amount")->after("rate")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("sale_order_products", ["rate", "amount"]);
    }
};
