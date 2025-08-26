<?php

use App\Helpers\MyMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends MyMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn("customers", "status"))
        {
            $q = "ALTER TABLE `customers` CHANGE COLUMN `status` `status_type` VARCHAR(80) NULL DEFAULT NULL ;";
            DB::statement($q);
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->string("payment_method_type", 80)->nullable()->after("status_type");
            $table->float("purpose_price")->nullable()->after("payment_method_type");
            $table->float("payable_amount")->nullable()->after("purpose_price");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("customers", ["payment_method_type", "purpose_price", "payable_amount"]);
    }
};
