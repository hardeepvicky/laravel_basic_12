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
        $this->dropColumnsWithIndex("leads", ["source_type_id", "deal_type_id", "status_type_id", "payment_method_type_id"]);

        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId("source_id")->nullable()->change();            
            $table->foreignId("dealer_id")->nullable()->change();

            $table->string("source_type")->nullable()->after("company_gst_no");
            $table->string("deal_type")->nullable()->after("city_id");
            $table->string("status_type")->nullable()->after("dealer_id");
            $table->string("payment_method_type")->nullable()->after("purpose_price");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
