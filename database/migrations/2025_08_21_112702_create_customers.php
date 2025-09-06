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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId("lead_id")->constrained("leads")->onDelete("restrict");
            $table->string("name");
            $table->string("mobile", 15);
            $table->string("mobile2", 15)->nullable();
            $table->string("email")->nullable();
            $table->string("company_name")->nullable();
            $table->string("brand_name")->nullable();
            $table->string("company_gst_no")->nullable();

            $table->foreignId("state_id")->constrained("states")->onDelete("restrict");
            $table->foreignId("district_id")->constrained("districts")->onDelete("restrict");
            $table->foreignId("city_id")->constrained("cities")->onDelete("restrict");

            $table->string("profile_img")->nullable();
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
        Schema::dropIfExists('customers');
    }
};
