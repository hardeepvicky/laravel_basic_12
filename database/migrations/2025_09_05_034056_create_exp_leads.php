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
        Schema::create('exp_leads', function (Blueprint $table) {
            $table->id();
            $table->string("name")->nullable();
            $table->text("url")->nullable();
            $table->string("image")->nullable();
            $table->string("video")->nullable();
            $table->string("mobile", 15);
            $table->string("mobile2", 15)->nullable();
            $table->string("email")->nullable();
            $table->string("company_name")->nullable();
            $table->string("brand_name")->nullable();
            $table->string("company_gst_no")->nullable();

            $table->foreignId("source_type_id")->nullable()->constrained("types")->onDelete("restrict");
            $table->foreignId("source_id")->nullable()->constrained("drop_down_values")->onDelete("restrict");
            $table->foreignId("country_id")->nullable()->constrained("exp_countries")->onDelete("restrict");
            $table->foreignId("state_id")->nullable()->constrained("exp_states")->onDelete("restrict");
            $table->foreignId("city_id")->nullable()->constrained("exp_cities")->onDelete("restrict");
            $table->foreignId("deal_type_id")->nullable()->constrained("types")->onDelete("restrict");
            $table->foreignId("dealer_id")->nullable()->constrained("dealers")->onDelete("restrict");
            $table->foreignId("campaign_id")->nullable()->constrained("campaigns")->onDelete("restrict");
            $table->foreignId("status_type_id")->nullable()->constrained("types")->onDelete("restrict");
            $table->float("purpose_price")->nullable();
            $table->float("payment_method_type_id")->nullable()->constrained("types")->onDelete("restrict");
            $table->string("profile_img")->nullable();
            $table->boolean("is_snooze")->default(0);
            $table->integer("snooze_minute")->default(0);
            $table->datetime("snooze_datetime")->nullable();
            $table->foreignId("response_type_id")->nullable()->constrained("drop_down_values")->onDelete("restrict");
            $table->text("remarks")->nullable();
            $table->timestamps();
            $table->bigInteger("created_by")->nullable();
            $table->bigInteger("updated_by")->nullable();


            $table->index(["name"]);
            $table->index(["mobile"]);
            $table->index(["mobile2"]);
            $table->index(["email"]);
            $table->index(["is_snooze", "snooze_datetime"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exp_leads');
    }
};
