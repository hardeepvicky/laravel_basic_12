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
        $this->dropColumnsWithIndex("product_serials", ["is_parent_serial_no"]);

        Schema::table('product_serials', function (Blueprint $table) {
            $table->string("hierarchy_type", 45)->nullable()->after("id");
            $table->string("serial_type", 45)->nullable()->after("hierarchy_type");
            $table->foreignId("sale_order_product_id")->nullable()->after("branch_id")->constrained("sale_order_products")->onDelete("restrict");
            $table->date("manufacture_date")->nullable()->after("sale_order_product_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("product_serials", ["hierarchy_type", "serial_type", "sale_order_product_id", "manufacture_date"]);
    }
};
