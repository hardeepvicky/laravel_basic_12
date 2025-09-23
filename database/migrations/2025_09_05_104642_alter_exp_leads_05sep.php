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
        $this->dropColumnsWithIndex("exp_leads", [
            "payment_method_type_id",
            "status_type_id",
            "deal_type_id",
            "source_type_id",
        ]);

        Schema::table('exp_leads', function (Blueprint $table) {
            $table->boolean("is_customer")->default(0)->after("id");
            $table->float("payable_amount")->nullable()->after("purpose_price");
            $table->string("payment_method_type")->nullable()->after("payable_amount");
            $table->string("source_type", 45)->nullable()->after("company_gst_no");
            $table->string("deal_type", 45)->nullable()->after("campaign_id");
            $table->string("status_type", 45)->nullable()->after("deal_type");
            $table->foreignId("assign_to_user_id")->after("status_type")->nullable()->constrained("types")->onDelete("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("exp_leads", [
            "is_customer",
            "payable_amount",
            "payment_method_type",
            "source_type",
            "deal_type",
            "status_type",
            "assign_to_user_id",
        ]);
    }
};
