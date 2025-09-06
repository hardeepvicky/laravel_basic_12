<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dispatch_orders', function (Blueprint $table) {
            $table->id();
            $table->string("type");
            $table->foreignId("carrier_id")->constrained("carriers")->onDelete("restrict");
            $table->foreignId("lead_id")->nullable()->constrained("leads")->onDelete("restrict");
            $table->string("addr1");
            $table->string("addr2")->nullable();
            $table->string("pincode", 10);
            $table->foreignId("country_id")->constrained("countries")->onDelete("restrict");
            $table->foreignId("state_id")->constrained("states")->onDelete("restrict");
            $table->foreignId("district_id")->nullable()->constrained("districts")->onDelete("restrict");
            $table->foreignId("city_id")->nullable()->constrained("cities")->onDelete("restrict");
            $table->string("docket_no")->nullable();
            $table->string("builty_no")->nullable();
            $table->string("tracking_no")->nullable();
            $table->string("tracking_phone_no")->nullable();
            $table->float("shipping_fee")->default(0);
            $table->float("shipping_fee_gst_per")->default(0);
            $table->float("shipping_fee_gst")->default(0);
            $table->float("total_fee")->default(0);
            $table->text("remarks")->nullable();
            $table->string("status_type", 45);
            $table->timestamps();
            $table->bigInteger("created_by")->nullable();
            $table->bigInteger("updated_by")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatch_orders');
    }
};
