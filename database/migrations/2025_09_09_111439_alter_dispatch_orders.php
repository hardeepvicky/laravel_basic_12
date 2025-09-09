<?php

use App\Helpers\MyMigration;

return new class extends MyMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->dropColumnsWithIndex("dispatch_orders", [
            "shipping_fee_gst_per",
            "shipping_fee_gst",
            "total_fee",
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
