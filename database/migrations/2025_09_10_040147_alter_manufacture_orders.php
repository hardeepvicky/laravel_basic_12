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
        Schema::table('manufacture_orders', function (Blueprint $table) {
            $table->foreignId("export_order_product_id")->nullable()->after("sale_order_product_id")->constrained("export_order_products")->onDelete("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("manufacture_orders", [
            "export_order_product_id",
        ]);
    }
};
