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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("mobile", 15);
            $table->string("mobile2", 15)->nullable();
            $table->string("email")->nullable();
            $table->string("company_name")->nullable();
            $table->string("brand_name")->nullable();
            $table->string("company_gst_no")->nullable();
            
            $table->foreignId("source_type_id")->constrained("types")->onDelete("restrict");
            $table->foreignId("source_id")->constrained("drop_down_values")->onDelete("restrict");
            $table->foreignId("state_id")->constrained("states")->onDelete("restrict");
            $table->foreignId("district_id")->constrained("districts")->onDelete("restrict");
            $table->foreignId("city_id")->constrained("cities")->onDelete("restrict");
            $table->foreignId("deal_type_id")->constrained("types")->onDelete("restrict");
            $table->foreignId("dealer_id")->nullable()->constrained("dealers")->onDelete("restrict");            
            $table->foreignId("status_type_id")->constrained("types")->onDelete("restrict");            
            $table->float("purpose_price")->nullable();
            $table->float("payment_method_type_id")->nullable()->constrained("types")->onDelete("restrict");
            $table->string("profile_img")->nullable();
            $table->text("remarks")->nullable();
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
        Schema::dropIfExists('leads');
    }
};
