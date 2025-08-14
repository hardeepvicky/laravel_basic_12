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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId("company_id")->constrained("companies")->onDelete("restrict");
            $table->string("type");
            $table->string("name");
            $table->string("email")->nullable();
            $table->foreignId("state_id")->constrained("states")->onDelete("restrict");
            $table->foreignId("district_id")->constrained("districts")->onDelete("restrict");
            $table->foreignId("city_id")->constrained("cities")->onDelete("restrict");
            $table->string("addr1")->nullable();
            $table->string("addr2")->nullable();
            $table->string("mobile", 15)->nullable();
            $table->string("phone_no", 15)->nullable();
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
        Schema::dropIfExists('branches');
    }
};
