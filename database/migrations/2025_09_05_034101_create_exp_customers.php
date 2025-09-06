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
        Schema::create('exp_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId("exp_lead_id")->constrained("exp_leads")->onDelete("restrict");
            $table->string("name");
            $table->string("mobile", 15);
            $table->string("mobile2", 15)->nullable();
            $table->string("email")->nullable();
            $table->string("company_name")->nullable();
            $table->string("company_gst_no")->nullable();

            $table->foreignId("country_id")->constrained("exp_countries")->onDelete("restrict");
            $table->foreignId("state_id")->constrained("exp_states")->onDelete("restrict");
            $table->foreignId("city_id")->constrained("exp_cities")->onDelete("restrict");

            $table->string("deal_type", 80)->nullable();
            $table->foreignId("dealer_id")->nullable()->constrained("dealers")->onDelete("restrict");
            $table->foreignId("assign_to_user_id")->nullable()->constrained("users")->onDelete("restrict");
            $table->string("status_type", 80)->nullable();
            $table->dateTime("next_followup_date_time")->nullable();
            $table->string("payment_method_type", 80)->nullable();
            $table->float("purpose_price")->nullable();
            $table->float("payable_amount")->nullable();
            $table->boolean("is_snooze")->default(0);
            $table->integer("snooze_minute")->default(0);
            $table->dateTime("snooze_datetime")->nullable();

            $table->string("profile_img")->nullable();
            $table->string("action_type", 45)->nullable();
            $table->foreignId("response_type_id")->nullable()->constrained("drop_down_values")->onDelete("restrict");
            $table->text("remarks")->nullable();

            $table->boolean("is_active")->default(1);
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
        Schema::dropIfExists('exp_customers');
    }
};
