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
        Schema::create('exp_competitors', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("alias");
            $table->string("mobile", 15);
            $table->string("mobile2", 15)->nullable();
            $table->string("phone_no", 15)->nullable();
            $table->string("email")->nullable();
            $table->foreignId("country_id")->nullable()->constrained("exp_countries")->onDelete("restrict");
            $table->foreignId("state_id")->nullable()->constrained("exp_states")->onDelete("restrict");
            $table->foreignId("city_id")->nullable()->constrained("exp_cities")->onDelete("restrict");
            $table->string("company_name")->nullable();
            $table->string("gst_no")->nullable();
            $table->text("description")->nullable();
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
        Schema::dropIfExists('exp_competitors');
    }
};
