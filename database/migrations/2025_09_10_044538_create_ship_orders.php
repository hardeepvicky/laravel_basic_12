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
        Schema::create('ship_orders', function (Blueprint $table) {
            $table->id();
            $table->string("order_from_type", 45);
            $table->foreignId("export_contact_id")->nullable()->constrained("export_contacts")->onDelete("restrict");
            $table->foreignId("export_order_id")->nullable()->constrained("export_orders")->onDelete("restrict");
            $table->string("addr1", 180);
            $table->string("addr2", 180)->nullable();
            $table->string("zipcode", 20);
            $table->foreignId("country_id")->constrained("exp_countries")->onDelete("restrict");
            $table->foreignId("state_id")->constrained("exp_states")->onDelete("restrict");
            $table->foreignId("city_id")->constrained("exp_cities")->onDelete("restrict");
            $table->foreignId("carrier_id")->nullable()->constrained("exp_parties")->onDelete("restrict");
            $table->string("docket_no", 20)->nullable();
            $table->string("tracking_no", 80)->nullable();
            $table->string("tracking_phone_no", 20)->nullable();
            $table->float("shipping_fee")->default(0);
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
        Schema::dropIfExists('ship_orders');
    }
};
