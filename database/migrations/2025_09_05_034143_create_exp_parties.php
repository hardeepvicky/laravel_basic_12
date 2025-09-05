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
        Schema::create('exp_parties', function (Blueprint $table) {
            $table->id();
            $table->string("type");
            $table->string("name");
            $table->string("mobile", 15);
            $table->string("mobile2", 15)->nullable();
            $table->string("phone_no", 15)->nullable();
            $table->string("email")->nullable();
            $table->string("company_name")->nullable();
            $table->foreignId("country_id")->constrained("exp_countries")->onDelete("restrict");
            $table->foreignId("state_id")->constrained("exp_states")->onDelete("restrict");
            $table->foreignId("city_id")->constrained("exp_cities")->onDelete("restrict");
            $table->text("description")->nullable();
            $table->string("action_type", 45)->nullable();
            $table->foreignId("response_type_id")->nullable()->constrained("drop_down_values")->onDelete("restrict");
            $table->string("remarks")->nullable();
            $table->string("status_type", 45)->nullable();
            $table->dateTime("next_followup_date_time")->nullable();
            $table->foreignId("assign_to_user_id")->nullable()->constrained("users")->onDelete("restrict");
            $table->boolean("is_active")->default(1);
            $table->integer("list_display_order")->default(0);
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
        Schema::dropIfExists('exp_parties');
    }
};
