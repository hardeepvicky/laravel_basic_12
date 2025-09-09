<?php

use App\Helpers\MyMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends MyMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->dropColumnsWithIndex("dispatch_orders", ["type", "lead_id"]);

        Schema::table('dispatch_orders', function (Blueprint $table) {
            $table->string("order_from_type", 45)->after("id")->nullable();
            $table->foreignId("contact_id")->nullable()->after("order_from_type")->constrained("contacts")->onDelete("restrict");
            $table->foreignId("sale_order_id")->nullable()->after("contact_id")->constrained("sale_orders")->onDelete("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("dispatch_orders", [
            "order_from_type",
            "contact_id",
            "sale_order_id"
        ]);
    }
};
